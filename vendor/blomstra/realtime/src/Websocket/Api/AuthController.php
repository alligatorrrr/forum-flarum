<?php

namespace Blomstra\Realtime\Websocket\Api;

use Blomstra\Realtime\Push\Payload\Generator;
use Flarum\Discussion\Discussion;
use Flarum\Http\RequestUtil;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Pusher\Pusher;

class AuthController implements RequestHandlerInterface
{
    private Pusher $pusher;
    private User $actor;
    private Generator $generator;

    public function __construct(Pusher $pusher, Generator $generator)
    {
        $this->pusher = $pusher;
        $this->generator = $generator;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = $request->getParsedBody();

        $this->actor = RequestUtil::getActor($request);

        $channel = Arr::get($attributes, 'channel_name');

        if (preg_match('~^private-(?<subject>[a-z]+)=(?<id>[0-9]+)$~', $channel, $m)) {
            if (method_exists($this, $m['subject']) && call_user_func([$this, $m['subject']], $m['id'])) {
                $body = $this->pusher->authorizeChannel(
                    $channel,
                    Arr::get($attributes, 'socket_id')
                );

                return new JsonResponse(json_decode($body, true));
            }
        }

        if (preg_match('~^presence-(?<subject>[a-z-]+)$~', $channel, $m)) {
            if (! $this->actor->isGuest() && method_exists($this, $m['subject'])) {
                $payload = call_user_func([$this, $m['subject']], $this->actor);

                // Only if the method returns anything, will we allow authentication.
                if ($payload) {
                    $body = $this->pusher->authorizePresenceChannel(
                        $channel,
                        Arr::get($attributes, 'socket_id'),
                        (string) $this->actor->id,
                        $payload
                    );

                    return new JsonResponse(json_decode($body, true));
                }
            }
        }

        return new EmptyResponse(403);
    }

    protected function user(int $id): bool
    {
        return !$this->actor->isGuest() && $this->actor->id === $id;
    }

    protected function typing(int $id): bool
    {
        return Discussion::whereVisibleTo($this->actor)->where('id', $id)->exists();
    }

    protected function online(User $actor): array
    {
        // @todo It returns []
        $generate = $this->generator;
        return [
            'displayName' => $actor->display_name
        ];
    }
}
