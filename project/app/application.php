<?php

use App\Cron\CronService;
use App\Task\TaskService;
use App\WebSocket\WsService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

if (!function_exists('twig')) {
    function twig()
    {
        $loader = new FilesystemLoader(APP_DIR . '/templates');
        return new Environment($loader, [
            'cache' => APP_DIR . '/templates/cache',
            'auto_reload' => true
        ]);
    };
}

if (!function_exists('ws')) {
    function ws($id = '')
    {
        return new WsService($id);
    };
}

if (!function_exists('task')) {
    function task()
    {
        return new TaskService();
    };
}

if (!function_exists('cron')) {
    function cron()
    {
        return new CronService();
    };
}

if (!function_exists('db')) {
    function db()
    {
        $capsule = new Illuminate\Database\Capsule\Manager;
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => DB_HOST,
            'database' => DB_DATABASE,
            'username' => DB_USERNAME,
            'password' => DB_PASSWORD,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);

        return $capsule->getDatabaseManager();
    };
}
