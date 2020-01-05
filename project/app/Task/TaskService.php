<?php

namespace App\Task;


class TaskService
{
    /** @var TaskGateway  */
    private  $gateway;

    public function __construct()
    {
        $this->gateway = new TaskGateway();
    }

    /**
     * @return array
     */
    public function getTask(): array
    {
        return $this->gateway->getTask();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getTaskById($id = 0): array
    {
        return empty($id) ? [] : $this->gateway->getTaskById($id);
    }

    /**
     * @param array $requestData
     * @param array $file
     * @return int
     * @throws \Exception
     */
    public function addTask($requestData = [], $file = []): int
    {
        if (empty($requestData) || empty($file)) {
            return 0;
        }

        return $this->gateway->addTask($requestData, $file);
    }

}
