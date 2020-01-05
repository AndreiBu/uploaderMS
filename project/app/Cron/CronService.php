<?php

namespace App\Cron;

class CronService
{
    /** @var CronGateway */
    private $gateway;

    public const ALL = 'all';
    public const NEW = 0;
    public const START = 1;
    public const BROKEN = 3;
    public const FINISH = 5;
    public const CANCELED = 8;

    public function __construct()
    {
        $this->gateway = new CronGateway();
    }

    /**
     * @param int $status
     * @param int $limit
     * @return array
     */
    public function getTask($status = self::ALL, $limit = 10): array
    {
        return $this->gateway->getTask($status, $limit);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getTaskById($id)
    {
        return $this->gateway->getTaskById($id);
    }

    /**
     * @param int $id
     * @param int $status
     * @param array $param
     * @return bool
     */
    public function setTaskStatus($id = 0, $status = 0, $param = []): bool
    {
        return empty($id) ? false : $this->gateway->setTaskStatus($id, $status, $param);
    }

    /**
     * @param int $taskId
     * @param array $file
     * @param string $channel
     * @param string $type
     * @return int
     * @throws \Exception
     */
    public function addTask($taskId = 0, $file = [], $channel = '', $type = 'cli_exec'): int
    {
        if (empty($taskId)) {
            return 0;
        }

        $filePath = $file['path'] . $file['name'];
        $filePath = str_replace('//', '/', $filePath);
        $thumbnailPath = $file['path'] . 'thumbnail/' . $file['name'];

//        $imagickCli = 'convert ' . STORAGE . $filePath . ' -resize \'100x200\' ' . STORAGE . $thumbnailPath;
        $imagickCli = 'convert -thumbnail 200x -quality 50 ' . STORAGE . $filePath . ' ' . STORAGE . $thumbnailPath;
        if (strtolower($file['ext']) === 'pdf') {
            $thumbnailPath = str_replace('.pdf', '_%d.jpg', $thumbnailPath);
            $imagickCli = 'convert -density 400 ' . STORAGE . $filePath . ' -scale \'100x200\'  ' . STORAGE . $thumbnailPath;
        }

        return $this->gateway->addTask($taskId, $imagickCli, $file, $channel, $type);
    }
}
