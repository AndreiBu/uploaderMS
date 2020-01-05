<?php
//ToDo hide error in live
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//ini_set('display_errors', 0);
//ini_set('display_startup_errors', 0);
//error_reporting(E_ERROR | E_PARSE);

// need for local machine
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 1728000');
    header("Content-Length: 0");
    header("Content-Type: text/plain");
    exit(0);
}

require_once __DIR__ . '/../config/config.php';

$route = new App\Route\Service();
$route->dispatchRoute();

