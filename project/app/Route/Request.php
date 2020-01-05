<?php
namespace App\Route;

class Request
{
    public const METHOD = 'REQUEST_METHOD';
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const URI = 'REQUEST_URI';
    public const SERVER_PROTOCOL = 'SERVER_PROTOCOL';

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $_SERVER[self::METHOD];
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $_SERVER[self::URI];
    }

    public function sendMethodNotAllowedHeader(): void
    {
        header($this->getServerProtocol() . ' 405 Method Not Allowed');
    }

    public function sendNotFoundHeader(): void
    {
        header($this->getServerProtocol() . ' 404 Not Found');
    }

    /**
     * @param string $type
     * @param string $parameter
     * @return null|string
     */
    public function getRequestParam(string $type, string $parameter): ?string
    {
        switch ($type) {
            case self::METHOD_GET:
                $parameterValue = $this->getParamFromGetVar($parameter);
                break;
            case self::METHOD_POST:
                $parameterValue = $this->getParamFromPostVar($parameter);
                break;
            default:
                $parameterValue = null;
        }
        return $parameterValue;
    }

    /**
     * @param string $parameter
     * @return null|string
     */
    private function getParamFromGetVar(string $parameter): string
    {
        return $this->getParamFromVar($parameter, $_GET);
    }

    /**
     * @param string $parameter
     * @return string
     */
    private function getParamFromPostVar(string $parameter): string
    {
        return $this->getParamFromVar($parameter, $_POST);
    }

    /**
     * @param string $parameter
     * @param array $var
     * @return string
     */
    private function getParamFromVar(string $parameter, array $var): string
    {
        if (array_key_exists($parameter, $var)) {
            return $var[$parameter];
        }
        return '';
    }

    /**
     * @return mixed
     */
    private function getServerProtocol()
    {
        return $_SERVER[self::SERVER_PROTOCOL];
    }
}
