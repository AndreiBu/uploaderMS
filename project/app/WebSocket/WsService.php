<?php

namespace App\WebSocket;

use Exception;
use WebSocket\Client as WsClient;

class WsService
{
    private $ws;
    private $id = '';

    /**
     * WsService constructor.
     * @param string $id
     */
    public function __construct($id = '')
    {
        try {
            $this->ws = new WsClient(WS_SERVER, ['timeout' => 1]);
            $this->setId($id);
        } catch (Exception $e) {
        }
    }

    /**
     * @param string $id
     */
    public function setId($id = '')
    {
        $this->id = $id;
    }

    /**
     * @param string $message
     * @param string $channel
     * @return bool
     */
    public function send($message = '', $channel = 'default'): bool
    {
        $msg = [
            'method' => 'messageToChannel',
            'channel' => $channel,
            'message' => $message,
            'id' => $this->id
        ];

        try {
            $this->ws->send(json_encode($msg));
            return true;
        } catch (Exception $e) {
        }
        return false;
    }

    /**
     * @param string $message
     * @param string $channel
     * @return bool
     */
    public function followChannel($message = '', $channel = 'default')
    {
        $msg = [
            'method' => 'followChannel',
            'channel' => $channel,
            'message' => $message,
            'id' => $this->id
        ];

        try {
            $this->ws->send(json_encode($msg));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
