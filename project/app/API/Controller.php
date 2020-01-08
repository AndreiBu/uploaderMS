<?php

namespace App\API;

use App\AbstractController;
use Cake\Core\App;
use Imagick;
use ReallySimpleJWT\Exception\ValidateException;
use ReallySimpleJWT\Token;

class Controller extends AbstractController
{
    public function thumbnailByUrl(): void
    {
        $url = $_GET['url'] ?? '';
        if (empty($url)) {
            \App\Route\Service::getErrorCode(400, 'url is empty');
        }

        try {
            $imagick = new Imagick($url);
        } catch (\ImagickException $e) {
            \App\Uploader\Service::getFileByHeader(APP_DIR . 'public/favicon.png');
        }

        $size = $_GET['size'] ?? 100;
        if ($size < 50) {
            $size = 50;
        }
        if ($size > 500) {
            $size = 500;
        }

        $imagick->thumbnailImage($size, $size, true, false);
        header("Content-Type: image/jpg");
        echo $imagick->getImageBlob();
    }

    public function createJWT(): void
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            \App\Route\Service::getErrorCode();
        }
        if ($token !== TOKEN) {
            \App\Route\Service::get401();
        }

        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'iss' => $_SERVER['REQUEST_URI'] ?? 'localhost'
        ];

        try {
            echo Token::customPayload($payload, TOKEN);
        } catch (ValidateException $e) {
            \App\Route\Service::getErrorCode(418, $e->getMessage());
        }

        die();
    }

    public function checkJWT(): void
    {

        if (self::verifyJWT()) {
            echo 'true';
        }
        die();
    }

    public static function verifyJWT(): bool
    {
        $token = $_GET['jwt'] ?? '';
        if (empty($token)) {
            \App\Route\Service::getErrorCode();
        }

        if (Token::validate($token, TOKEN)) {
            return true;
        } else {
            \App\Route\Service::get401();
        }

        return false;
    }

}
