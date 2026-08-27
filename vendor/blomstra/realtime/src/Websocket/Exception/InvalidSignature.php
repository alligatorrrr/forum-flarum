<?php


namespace Blomstra\Realtime\Websocket\Exception;

class InvalidSignature extends WebsocketException
{
    public function __construct()
    {
        $this->trigger("Invalid Signature.", 4009);
    }
}
