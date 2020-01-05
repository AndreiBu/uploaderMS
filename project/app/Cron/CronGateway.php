<?php

namespace App\Cron;

use DateTimeImmutable;

class CronGateway
{
    /** @var \Illuminate\Database\DatabaseManager */
    private $db;

    public function __construct()
    {
        $this->db = db();
    }

    /**
     * @param int $status
     * @param int $limit
     * @return array
     */
    public function getTask($status = CronService::NEW, $limit = 10): array
    {
        $query = $this->db->table('cron_task');

        if ($status !== CronService::ALL) {
            $query->where('status', $status);
        }

        $query->limit($limit)
            ->orderBy('id', 'desc');
        //ToDo mapper to dataObject
        return $query->get()->toArray();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getTaskById($id)
    {
        $item = $this->db->table('cron_task')->where('id', $id)->get()->first();
        //ToDo mapper to dataObject
        return $item;
    }

    /**
     * @param int $id
     * @param int $status
     * @param array $params
     * @return bool
     */
    public function setTaskStatus($id = 0, $status = 0, $params = []): bool
    {
        $message = substr($params['message'] ?? '', 0, 255);
        $duration = $params['duration'] ?? 0;

        return empty($status) ? false :  $this->db->table('cron_task')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'message' => $message,
                'duration' => $duration,
            ]);
    }

     /**
     * @param int $taskId
     * @param string $task
     * @param array $file
     * @param string $channel
     * @param string $type
     * @return int
     * @throws \Exception
     */
    public function addTask($taskId, $task = '', $file = [], $channel = '', $type = 'cli_exec'): int
    {
        $date = new DateTimeImmutable();

        $filePath = $file['path'] . $file['name'];
        $filePath = str_replace('//', '/', $filePath);

        return $this->db->table('cron_task')
            ->insertGetId([
                'date_cr' => $date->format('Y-m-d H:i:s'),
                'task_id' => $taskId,
                'path' => $filePath,
                'size' => $file['size'] ?? 0,
                'status' => 0,
                'type' => $type,
                'task' => $task,
                'duration' => 0,
                'channel' => $channel
            ]);
    }
}
