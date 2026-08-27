<?php

namespace Blomstra\Realtime\Push\Jobs;

use Flarum\Discussion\Discussion;
use GuzzleHttp\Promise\Utils;
use Illuminate\Contracts\Queue\Queue;

class SendFlaggedJob extends Job
{
    private Discussion $discussion;

    public function __construct(Discussion $discussion)
    {
        parent::__construct();
        
        $this->discussion = $discussion;
    }

    public function handle(Queue $queue)
    {
        $users = $this->connectedUsers($this->discussion);

        foreach ($users as $user) {
            if ($user->cannot('discussion.viewFlags', $this->discussion)) continue;

            $queue->push(
                new SendGeneratedPayloadJob('flagged', $user, $user)
            );
        }
    }
}
