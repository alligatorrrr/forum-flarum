<?php

namespace Kilowhat\RichEmbeds\Listeners;

use Flarum\Post\CommentPost;
use Flarum\Post\Event\Saving;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Illuminate\Support\Arr;
use Kilowhat\RichEmbeds\Embed;
use Kilowhat\RichEmbeds\Repositories\EmbedManager;
use Kilowhat\RichEmbeds\Repositories\WhitelistManager;
use s9e\TextFormatter\Utils;

class SavePost
{
    protected $manager;
    protected $whitelist;
    protected $settings;

    public function __construct(EmbedManager $manager, WhitelistManager $whitelist, SettingsRepositoryInterface $settings)
    {
        $this->manager = $manager;
        $this->whitelist = $whitelist;
        $this->settings = $settings;
    }

    public function handle(Saving $event)
    {
        if (!($event->post instanceof CommentPost)) {
            return;
        }

        $attributes = Arr::get($event->data, 'attributes') ?? [];

        $reparse = false;

        if (Arr::exists($attributes, 'kilowhatRichEmbedsDisable')) {
            // We need to check edit permission in addition to the disable permission
            // because if the post exists the only check is on the original post author
            $event->actor->assertCan('edit', $event->post);

            if (!$this->can($event, 'disableOnAnyPost', 'disableOnOwnPost')) {
                throw new PermissionDeniedException();
            }

            $event->post->kilowhat_rich_embeds_disable = (bool)Arr::get($attributes, 'kilowhatRichEmbedsDisable');

            if ($event->post->isDirty('kilowhat_rich_embeds_disable')) {
                $reparse = true;
            }
        }

        if ($event->post->isDirty('content') || $reparse) {
            $linkEmbedsEnabled = $this->can($event, 'useOnAnyPost', 'useOnOwnPost') && !$event->post->kilowhat_rich_embeds_disable;

            $embedIds = [];

            if ($this->settings->get('kilowhat-rich-embeds.imageEmbeds')) {
                // Do IMG first, that way the code below will replace false with true for is_link attribute without the need for any if block
                Utils::replaceAttributes($event->post->getParsedContentAttribute(), 'IMG', function ($attributes) use (&$embedIds) {
                    $url = Embed::normalizeUrl(Arr::get($attributes, 'src'));

                    // TODO: separate whitelist
                    if ($url && $this->whitelist->isWhitelisted($url)) {
                        $embedIds[$this->manager->firstOrCreate($url)->id] = false;
                    }

                    // The return value is not important, we just use the Util function as a helper to loop through the tags
                    return [];
                });
            }

            if ($linkEmbedsEnabled) {
                // TODO: check bbcode is enabled
                Utils::replaceAttributes($event->post->getParsedContentAttribute(), 'RICH-URL', function ($attributes) use (&$embedIds) {
                    $url = Embed::normalizeUrl(Arr::get($attributes, 'rich-url'));

                    if ($url && $this->whitelist->isWhitelisted($url)) {
                        $embedIds[$this->manager->firstOrCreate($url)->id] = true;
                    }

                    return [];
                });

                // TODO: check is enabled
                Utils::replaceAttributes($event->post->getParsedContentAttribute(), 'URL', function ($attributes) use (&$embedIds) {
                    $url = Embed::normalizeUrl(Arr::get($attributes, 'url'));

                    if ($url && $this->whitelist->isWhitelisted($url)) {
                        $embedIds[$this->manager->firstOrCreate($url)->id] = true;
                    }

                    return [];
                });
            }

            $event->post->afterSave(function (CommentPost $post) use ($embedIds) {
                $post->kilowhatRichEmbeds()->sync(array_map(function ($isLink) {
                    return [
                        'is_link' => $isLink,
                    ];
                }, $embedIds));
            });

        }
    }

    protected function can(Saving $event, string $any, string $own): bool
    {
        if (!$event->post->exists) {
            return $event->actor->hasPermission('kilowhat-rich-embeds.' . $own);
        }

        if ($event->actor->can('kilowhat-rich-embeds.' . $any)) {
            return true;
        }

        if ($event->post->user) {
            return $event->post->user->can('kilowhat-rich-embeds.' . $own);
        }

        return false;
    }
}
