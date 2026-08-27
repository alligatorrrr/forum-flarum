<?php

namespace Blomstra\Realtime\Push\Jobs;

use Blomstra\Realtime\Push\Payload\Generator;
use Flarum\Database\AbstractModel;
use Flarum\User\User;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Pusher\Pusher;

class SendGeneratedPayloadJob extends Job
{
    private ?User $recipient;
    private AbstractModel $model;
    private string $event;
    private ?array $includes;

    public function __construct(string $event, AbstractModel $model, User $recipient = null, array $includes = null)
    {
        parent::__construct();

        $this->recipient = $recipient;
        $this->model = $model;
        $this->event = $event;
        $this->includes = $includes;
    }

    public function handle(Generator $generator, Pusher $pusher)
    {
        $channel = $this->recipient
            ? "private-user={$this->recipient->id}"
            : 'public';

        $payload = $generator($this->model, $this->recipient, $this->includes);

        // Kill the job in case we cannot generate the payload.
        if (! $payload) return;

        $pusher->trigger(
            $channel,
            $this->event,
            $payload
        );
    }

    public function middleware()
    {
        $key = sprintf(
            '%s:%s:%s-%s:%s',
            $this->event,
            get_class($this),
            $this->model->getTable(),
            $this->model->getKey(),
            $this->recipient ? $this->recipient->getKey() : 'guest'
        );

        return [
            (new WithoutOverlapping($key))->dontRelease()
        ];
    }
}
