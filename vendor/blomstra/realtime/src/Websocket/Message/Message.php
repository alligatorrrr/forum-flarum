<?php

namespace Blomstra\Realtime\Websocket\Message;

use Blomstra\Realtime\Websocket\Channel\Manager;
use Ratchet\ConnectionInterface;
use stdClass;

class Message
{
    protected ConnectionInterface $connection;
    protected Manager $manager;
    protected stdClass $payload;

    public function __construct(
        stdClass $payload,
        ConnectionInterface $connection,
        Manager $manager
    )
    {
        $this->payload = $payload;
        $this->connection = $connection;
        $this->manager = $manager;
    }

    public function respond()
    {
        $channel = $this->manager->find($this->payload->channel);

        optional($channel)
            ->broadcastToEveryoneExcept(
                $this->payload,
                /** @phpstan-ignore-next-line */
                $this->connection->socketId
            );
    }
}
