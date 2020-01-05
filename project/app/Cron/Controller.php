<?php

namespace App\Cron;

use App\AbstractController;

class Controller extends AbstractController
{
    /** @var CronService */
    private $cron;

    public function __construct()
    {
        parent::__construct();

        $this->cron = new CronService();
    }

    public function showTasks(): void
    {
        $taskService = task();
        $crons = $this->cron->getTask();
        $tasks = $taskService->getTask();
        echo twig()->render('tasks.twig', [
            'crons' => $crons,
            'tasks' => $tasks,
        ]);

        die();
    }
}
