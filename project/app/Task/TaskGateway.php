<?php

namespace App\Task;

use DateTimeImmutable;

class TaskGateway
{
    /** @var \Illuminate\Database\DatabaseManager */
    private $db;

    public function __construct()
    {
        $this->db = db();
    }

    /**
     * @return array
     */
    public function getTask(): array
    {
        return $this->db->table('task')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * @param $id
     * @return array
     */
    public function getTaskById($id): array
    {
        return (array)$this->db->table('task')
            ->where('id', $id)
            ->first();
    }

    /**
     * @param array $requestData
     * @param array $file
     * @return int
     * @throws \Exception
     */
    public function addTask($requestData = [], $file = []): int
    {
        $date = new DateTimeImmutable();

        $filePath = $file['path'] . $file['name'];
        $filePath = str_replace('//', '/', $filePath);

        return $this->db->table('task')
            ->insertGetId([
                'date_cr' => $date->format('Y-m-d H:i:s'),
                'status' => 0,
                'user_id' => $requestData['userId'] ?? 0,
                'item_id' => $requestData['itemId'] ?? 0,
                'file_name' => $requestData['name'] ?? '',
                'size' => $file['size'] ?? 0,
                'extension' => $file['ext'] ?? '',
                'path' => $filePath,
                'request' => json_encode($requestData),
                'file' => json_encode($file),
                'channel' => $requestData['channel'] ?? '',
                'domain' => DOMAIN_URL ?? '',
                'webhook' => '',
            ]);
    }
}
