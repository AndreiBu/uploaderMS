<?php

namespace App\Route;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

class Service
{
    private const HANDLER_DELIMITER = '@';

    /** @var Request */
    private $request;

    public function __construct( )
    {
        $this->request = new Request();
    }

    public function dispatchRoute(): void
    {
        $dispatcher = $this->getDispatcher();
        $requestMethod = $this->request->getMethod();
        $requestUri = $this->request->getUri();

        if (false !== $pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        $requestUri = rawurldecode($requestUri);

        $this->dispatch($requestMethod, $requestUri, $dispatcher);
    }

    /**
     * @param string $file
     */
    private function getStatic($file): void
    {
        if (file_exists($file)) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
            header('Cache-Control: public');
            header('Content-Type: ' . mime_content_type($file));
            header('Content-Length: ' . filesize($file));
            header('Content-Disposition: inline');

            readfile($file);
        }
        exit;
    }


    /**
     * @param string $requestMethod
     * @param string $requestUri
     * @param Dispatcher $dispatcher
     */
    private function dispatch(string $requestMethod, string $requestUri, Dispatcher $dispatcher): void
    {
        $routeInfo = $dispatcher->dispatch($requestMethod, $requestUri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                if (file_exists(PUBLIC_DIR . '/' . $requestUri)) {
                    $this->getStatic(PUBLIC_DIR . '/' . $requestUri);
                } else {
                    self::get404('rout not found :(');
                }
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                self::get404('NOT_ALLOWED');
                break;
            case Dispatcher::FOUND:
                [$state, $handler, $vars] = $routeInfo;
                [$class, $method] = explode(static::HANDLER_DELIMITER, $handler, 2);
                if (class_exists($class)) {
                    $controller = new $class();
                    if (method_exists($controller, $method)) {
                        $controller->{$method}(...array_values($vars));
                    }
                }
                unset($state);
                break;
        }
    }

    /**
     * @return Dispatcher
     */
    private function getDispatcher(): Dispatcher
    {
        return \FastRoute\simpleDispatcher(static function (RouteCollector $r) {
            $routes = [];
            if (file_exists(APP_DIR . '/config/routes.php')) {
                $routes = include APP_DIR . '/config/routes.php';
            }
            foreach ($routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        });
    }

    /**
     * @param string $message
     */
    public static function get404($message = 'not found.'): void
    {
        header("HTTP/1.0 404 Not Found");
        echo $message ."\n";
        die();
    }

    public static function get401(): void
    {
        header("HTTP/1.0 401 Unauthorized ");
        die();
    }

    public static function getErrorCode($code = 400, $message = 'Bad Request'): void
    {
        header("HTTP/1.0 $code $message ");
        die();
    }

}
