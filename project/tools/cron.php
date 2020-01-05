<?php

use App\Cron\CronService;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ERROR | E_PARSE);

require_once __DIR__ . '/../config/config.php';

$cron = cron();

$cronStartDate = date('Y.m.d H:i:s');
$cronStart = microtime(true);

while (1) {
    $cronTasks = $cron->getTask(CronService::NEW, 1);
    foreach ($cronTasks as $item) {

        $start = microtime(true);

        $cron->setTaskStatus($item->id, CronService::START);
//        $pid = pcntl_fork();
//        if ($pid === -1) {
//            echo 'fork error';
//        } elseif ($pid) {
//            continue;
//        } else {
            $cron = cron();
            $taskService = task();
            $ws = ws();

            $ws->send([
                'status' => 'fork',
                'task' => 'cron',
            ], 'uploader2');
            try {
                if (file_exists(STORAGE . $item->path)) {

                    $path_parts = pathinfo($item->path);
                    $task = $taskService->getTaskById($item->task_id);

                    if ($item->type === 'cli_exec') {

                        $webHookStatus = App\Uploader\Service::callWebHook([
                            'work' => 'saveInS3',
                            'taskId' => $item->task_id,
                            'type' => 'original',
                            'downloadLink' => \App\Uploader\Service::createDownloading([
                                'taskId' => $item->task_id
                            ])
                        ], $task['domain']);

                        sendToS3(STORAGE . $item->path, 'original/' . $item->task_id);
                        $ws->send([
                            'work' => 'saveInS3',
                            'webHookStatus' => $webHookStatus,
                            'taskId' => $item->task_id,
                            'type' => 'original',
//                            'downloadLink' => \App\Uploader\Service::createDownloading([
//                                'taskId' => $item->task_id
//                            ])
                        ], $item->channel);

                        if ($path_parts['extension'] === 'pdf') {
                            createThumbnail($item->path, $item->task_id, $start, $ws, $item->channel);
                        } else {
                            $output = shell_exec($item->task);
                            $cliParts = explode(' ', $item->task);
                            $optionId = $taskService->getOptionByTaskId($item->task_id);

                            for ($page = 0; $page < $task['pages']; $page++) {

                                $thumbnail = '';
                                $thumbnailPath = str_replace(STORAGE, '', $cliParts[count($cliParts) - 1]);

                                if ($path_parts['extension'] === 'pdf') {
                                    $thumbnailPath = str_replace('_%d.', '_' . $page . '.', $thumbnailPath);
                                }

                                if (file_exists(STORAGE . $thumbnailPath)) {
                                    $thumbnail = $thumbnailPath;
                                }

                                if (!empty($thumbnail)) {
                                    sendToS3(STORAGE . $thumbnail, 'thumbnail/' . $item->task_id);
                                    $webHookStatus = App\Uploader\Service::callWebHook([
                                        'work' => 'saveInS3',
                                        'taskId' => $item->task_id,
                                        'type' => 'thumbnail',
                                        'page' => $page,
                                        'optionId' => $task['option_id'],
                                        'downloadLink' => \App\Uploader\Service::createDownloading([
                                            'taskId' => $item->task_id,
                                            'page' => $page
                                        ], 'thumbnail')
                                    ], $task['domain']);
                                    $data = file_get_contents(STORAGE . $thumbnail);
                                    $base64 = 'data:image/jpg;base64,' . base64_encode($data);
                                }

                                $duration = microtime(true) - $start;
                                $ws->send([
//                                    'downloadLink' => \App\Uploader\Service::createDownloading([
//                                        'taskId' => $item->task_id,
//                                        'page' => $page
//                                    ], 'thumbnail'),
                                    'webHookStatus' => $webHookStatus ?? 'no',
                                    'taskId' => $item->task_id,
                                    'task' => 'thumbnail',
//                                    'thumbnail' => $thumbnail,
                                    'page' => $page,
                                    'optionId' => $optionId,
                                    'duration' => $duration,
                                    'base64' => $base64 ?? '',

                                ], $item->channel);
                            }
                        }
                    } elseif ($item->type === 'moveToS3') {
                        sendToS3(STORAGE . $item->path, 'original/' . $item->task_id);
                        App\Uploader\Service::callWebHook([
                            'work' => 'saveInS3',
                            'taskId' => $item->task_id,
                            'type' => 'original',
                            'downloadLink' => \App\Uploader\Service::createDownloading([
                                'taskId' => $item->task_id
                            ])
                        ], $task['domain']);
                    }

                    $duration = microtime(true) - $start;
                    $cron->setTaskStatus($item->id, CronService::FINISH, [
                        'duration' => $duration,
                    ]);
                } else {
                    $duration = microtime(true) - $start;
                    $cron->setTaskStatus($item->id, CronService::BROKEN, [
                        'duration' => $duration,
                        'message' => 'file no found ' . $item->path,
                    ]);
                    $ws->send([
                        'taskId' => $item->task_id,
                        'task' => 'bad_format',
                        'duration' => $duration
                    ], $item->channel);

                }
            } catch (\Exception $e) {
                $duration = microtime(true) - $start;
                $cron->setTaskStatus($item->id, CronService::CANCELED, [
                    'message' => $e->getMessage(),
                    'duration' => $duration,
                ]);
            }
//            die();
//        }
    }
    $cronWorking = microtime(true) - $cronStart;
    if ($cronWorking > 55) {
//        pcntl_wait($status);
        die();
    }
//    sleep(1);
    time_nanosleep(0, 20000000);
}


/**
 * @param $pdfPath
 * @param $taskId
 * @param $start
 * @param $ws
 * @param $channel
 * @return void
 * @throws ImagickException
 * @throws \GuzzleHttp\Exception\GuzzleException
 */
function createThumbnail($pdfPath, $taskId, $start, $ws, $channel)
{
    $im = new imagick();
    $im->setResolution(50, 50);
    $im->readImage(STORAGE . $pdfPath);
    $im->setImageFormat('jpeg');
    $im->setImageCompression(imagick::COMPRESSION_JPEG);
    $im->setImageCompressionQuality(40);
    $num_pages = $im->getNumberImages();

    $optionId = task()->getOptionByTaskId($taskId);

    $task = task()->getTaskById($taskId);

    for ($page = 0; $page < $num_pages; $page++) {
        $im->setIteratorIndex($page);
        $im->setImageFormat('jpeg');
        $thumbnail = str_replace('.pdf', '-' . $page . '.jpg', $pdfPath);
        $thumbnail = explode('/', $thumbnail);
        $thumbnail[] = $thumbnail[count($thumbnail) - 1];
        $thumbnail[count($thumbnail) - 2] = 'thumbnail';
        $thumbnail = implode('/', $thumbnail);
        $im->writeImage(STORAGE . $thumbnail);
        $message = sendToS3(STORAGE . $thumbnail, 'thumbnail/' . $taskId . '_' . $page);
        App\Uploader\Service::callWebHook([
            'work' => 'saveInS3',
            'taskId' => $taskId,
            'type' => 'thumbnail',
            'page' => $page,
            'optionId' => $task['option_id'],
            'downloadLink' => \App\Uploader\Service::createDownloading([
                'taskId' => $taskId,
                'page' => $page
            ], 'thumbnail')
        ], $task['domain']);

        if (!empty($thumbnail)) {
            $data = file_get_contents(STORAGE . $thumbnail);
            $base64 = 'data:image/jpg;base64,' . base64_encode($data);
        }

        $duration = microtime(true) - $start;
        $ws->send([
            'message' => $message,
            'taskId' => $taskId,
            'task' => 'thumbnail',
            'source' => 'cron_thumbnail',
            'downloadLink' => \App\Uploader\Service::createDownloading([
                'taskId' => $taskId,
                'page' => $page
            ], 'thumbnail'),
            'page' => $page,
            'optionId' => $optionId,
            'duration' => $duration,
            'base64' => $base64 ?? '',
        ], $channel);

    }
    $im->clear();
    $im->destroy();
}


/**
 * @param string $filePatch
 * @param string $key
 */
function sendToS3($filePatch = '', $key = '')
{
    if (APP_MODE !== 'live') {
        return false;
    }

    $s3 = new \Aws\S3\S3Client([
        'region' => 'eu-central-1',
        'version' => 'latest',
        'credentials' => [
            'key' => \App\Uploader\Service::$keyS3,
            'secret' => \App\Uploader\Service::$secretS3,
        ]
    ]);

    $result = $s3->putObject([
        'Bucket' => S3_BUCKET,
        'Key' => (APP_MODE === 'dev' ? 'dev' : '') . $key,
        'SourceFile' => $filePatch
    ]);

    return $result;
}
