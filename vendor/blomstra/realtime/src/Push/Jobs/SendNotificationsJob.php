<?php

namespace Blomstra\Realtime\Push\Jobs;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use Illuminate\Contracts\Queue\Queue;

class SendNotificationsJob extends Job
{
    /**
     * @var BlueprintInterface
     */
    private $blueprint;

    /**
     * @var User[]
     */
    private $recipients;

    public static ?string $onQueue = null;

    public function __construct(BlueprintInterface $blueprint, array $recipients)
    {
        parent::__construct();

        $this->blueprint = $blueprint;
        $this->recipients = $recipients;
    }

    public function handle(Queue $queue)
    {
        // Only dispatch notification jobs for users on the socket.
        $intersect = $this->connectedUsers()->intersect($this->recipients);

        foreach ($intersect as $user) {
            if ($user->shouldAlert($this->blueprint::getType())) {
                $queue->push(
                    new SendGeneratedPayloadJob('notification', $user, $user, [])
                );
            }
        }
    }
}
