<?php

namespace Blomstra\Realtime\Push\Payload;

use Flarum\Api\Client;
use Flarum\Api\Controller\AbstractSerializeController;
use Flarum\Api\Controller\ShowDiscussionController;
use Flarum\Api\Controller\ShowPostController;
use Flarum\Api\Controller\ShowUserController;
use Flarum\Database\AbstractModel;
use Flarum\Discussion\Discussion;
use Flarum\Http\RequestUtil;
use Flarum\Post\Post;
use Flarum\User\Guest;
use Flarum\User\User;
use Flarumite\DiscussionViews\Listeners\AddDiscussionViewHandler;
use Illuminate\Contracts\Container\Container;
use Laminas\Diactoros\ServerRequest;
use Psr\Log\LoggerInterface;

class Generator
{
    protected array $endpoints = [
        Discussion::class => 'discussions',
        Post::class => 'posts',
        User::class => 'users'
    ];

    protected array $includes = [
        Post::class => ShowPostController::class,
        Discussion::class => ShowDiscussionController::class,
        User::class => ShowUserController::class,
    ];

    public function __construct(private Client $client, private Container $container)
    {}

    public function __invoke(AbstractModel $subject, User $recipient = null, array $includes = null): ?array
    {
        if ($subject instanceof Post) $subject = $subject->discussion;

        $this->disableTracking();

        $endpoint = $this->retrieve($subject, $this->endpoints);

        if (! $endpoint || $subject->id === null) {
            return null;
        }

        $include = $includes !== null ? $includes : $this->with($subject);

        $response = $this->client
            ->withActor($recipient ?? new Guest)
            ->withQueryParams(['include' => $include])
            ->get("/$endpoint/$subject->id");

        $contents = (string) $response->getBody();

        if ($response->getStatusCode() === 200 && ! empty($contents)) {
            return json_decode($contents, true);
        }

        return null;
    }

    protected function retrieve(AbstractModel $class, array $find)
    {
        foreach ($find as $match => $result) {
            /** @phpstan-ignore-next-line */
            if (is_a($class, $match) || is_subclass_of($class, $match)) {
                return $result;
            }
        }

        return null;
    }

    protected function with(AbstractModel $subject): string
    {
        /** @var string|null $controller */
        $controller = $this->retrieve($subject, $this->includes);

        if (! $controller) return '';

        /** @var AbstractSerializeController $controller */
        $controller = $this->container->make($controller);

        // We need to send a request through handle to make the includes cascade into the class.
        try {
            $controller->handle(RequestUtil::withActor(new ServerRequest, new Guest));
        } catch (\Exception $e) {}

        if ($subject instanceof Discussion) {
            $include = array_merge($controller->include, ['lastPostedUser', 'lastPost']);
        } else {
            $include = array_merge($controller->include, $controller->optionalInclude);
        }

        return join(',', $include);
    }

    protected function disableTracking()
    {
        // flarumite/simple-discussion-views
        class_exists(AddDiscussionViewHandler::class) && AddDiscussionViewHandler::$enabled = false;
    }
}
