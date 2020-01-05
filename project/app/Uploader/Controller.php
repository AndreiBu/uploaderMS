<?php

namespace App\Uploader;

use App\Cron\CronService;
use Monolog\Logger;
use App\AbstractController;

class Controller extends AbstractController
{
    /** @var Logger */
    private $logger;

    /** @var Service */
    private $uploaderService;

    /** @var CronService */
    private $cron;

    public function __construct()
    {
        parent::__construct();

        $this->uploaderService = new Service();
        $this->cron = new CronService();
    }

    /**
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addFile(): void
    {
        try {
            $imageExt = ['pdf', 'jpg', 'jpeg', 'gif', 'png'];

            $channel = $_POST['channel'] ?? 'test';
            $uploadKey = $_POST['uploadKey'] ?? '';
            $requestData = $this->uploaderService->decryptKey($uploadKey);
            $requestData['optionId'] = (int)($_POST['position'] ?? 0);
            $requestData['channel'] = $channel;
            $sessionId = $requestData['sessionId'] ?? 'None';

            $start = microtime(true);
            $subFolder = date('Y_m') . '/';
            if (!empty($requestData)) {
                //$subFolder .= !empty($requestData['orderId']) ? $requestData['orderId'] . '/' : 'session/' . $sessionId . '/';
            }
            $option = $this->uploaderService->getUploadOption($subFolder);


            if (!empty($_FILES['files']['name'])) {
                $originalFileName = $_FILES['files']['name'];
                $_FILES['files']['name'] = $this->uploaderService->transliterate($_FILES['files']['name']);
            }

            $uploadHandler = new UploadHandler($option);

            $file = (array)$uploadHandler->get_response()['files'][0];
            $file['originalNmae'] = $originalFileName ?? '';

            $ws = ws($requestData['orderId'] ?? $requestData['session'] ?? 'none');
            if (!empty($requestData) && !empty($file)) {
                $task = task();
                $file['path'] = $subFolder;
                $taskId = $task->addTask($requestData, $file);
                $wsStatus = $ws->send([
                    'taskId' => $taskId,
                    'task' => 'created',
                ], $channel);

                $filePath = '' . $subFolder . '' . $file['name'];

                $cronTaskType = 'moveToS3';
                if (in_array(strtolower($file['ext']), $imageExt, true)) {
                    $withCron = true;
                    $cronTaskType = 'cli_exec';
                }

                $cron = cron();
                $cronTaskId = $cron->addTask($taskId, $file, $requestData['channel'], $cronTaskType);
                $pages = Service::howMuchPages($cron->getTaskById($cronTaskId), $start, 'controller');

                $webHookStatus = Service::callWebHook([
                    'work' => 'taskCreated',
                    'taskId' => $taskId,
                    'requestData' => $requestData,
                    'file' => $file,
                    'pages' => $pages,
                ]);

                $ws->send([
                    'taskId' => $taskId,
                    'task' => 'done',
                    'fileSize' => $file['size'],
                ], $channel);

            } else {
                $wsStatus = $ws->send([
                    'message' => 'bad file',
                    'task' => 'error',
                ], $channel);
            }

            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            echo json_encode([
                'status' => true,
                'webHookStatus' => $webHookStatus,
                'wsStatus' => $wsStatus ?? false,
                'thumbnail' => '/storage/'.$file['path'].$file['thumbnailUrl'],
                'file' => '/storage/'.$file['path'].''.$file['name'],
                'taskId' => $taskId,
                'withCron' => $withCron ?? false,
                'message' => ''
            ]);
            die();
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getFile()
    {
        $argv = func_get_args();
        $folder = $argv[0] ?? '';
        $fileName = $argv[1] ?? '';
        $file = STORAGE . '/' . $folder . '/' . $fileName;

        if (!empty($folder) && !empty($fileName)) {
            $this->getStatic($file);
        }
        exit;
    }

    public function getThumbnail()
    {
        $argv = func_get_args();
        $folder = $argv[0] ?? '';
        $fileName = $argv[1] ?? '';
        $file = STORAGE . '/' . $folder . '/thumbnail/' . $fileName;

        if (!empty($folder) && !empty($fileName)) {
            $this->getStatic($file);
        }
        exit;
    }

    /**
     * @param string $key
     */
    public function getFileByKey($key): void
    {
        $data = $this->uploaderService->decryptKey(base64_decode($key));

        $taskId = $data['taskId'] ?? 0;

        if (!empty($taskId)) {
            $this->uploaderService->getFileFromS3('original/' . $taskId);
        }
        \App\Route\Service::get404();
    }

    /**
     * @param string $key
     */
    public function thumbnailByKey($key): void
    {
        $data = $this->uploaderService->decryptKey(base64_decode($key));

        $taskId = $data['taskId'] ?? 0;

        if (!empty($taskId)) {
            $task = task()->getTaskById($taskId);
            $page = $data['page'] ?? 0;

            $s3Key = 'thumbnail/' . $taskId;
            if ($task['extension'] === 'pdf') {
                $s3Key = 'thumbnail/' . $taskId . '_' . $page;
            }

            $this->uploaderService->getFileFromS3($s3Key);
        }
        \App\Route\Service::get404();
    }

    /**
     * @param string $file
     */
    private function getStatic($file = ''): void
    {
        if (!empty($file) && file_exists($file)) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
            header("Cache-Control: public");
            header('Content-Type: ' . mime_content_type($file));
            header('Content-Length: ' . filesize($file));
            header('Content-Disposition: inline');
            header('Content-Type: application/octet-stream');

            readfile($file);
            exit;
        }
        \App\Route\Service::get404('404');
    }

    public function getOptionAccept(): void
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            }
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            exit(0);
        }
        exit(1);
    }
}
