<?php

namespace App\HealthCheck;

use App\AbstractController;

class Controller extends AbstractController
{
    public function healthCheck(): void
    {
        if (!$this->getDirInfo(STORAGE)) {
            \App\Route\Service::getErrorCode(500, 'storage not writable');
        }

        $files = scandir(STORAGE);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                if (!$this->getDirInfo(STORAGE . '' . $file)) {
                    \App\Route\Service::getErrorCode(500, 'storage not writable');
                }
            }
        }
        \App\Route\Service::getErrorCode(200, 'all is ok');
    }

    /**
     * @param $path
     * @return bool
     */
    private function getDirInfo($path = '')
    {
        return is_writable($path);
    }
}
