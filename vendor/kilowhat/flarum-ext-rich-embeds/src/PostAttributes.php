<?php

namespace Kilowhat\RichEmbeds;

use Flarum\Api\Serializer\PostSerializer;
use Flarum\Post\Post;
use Illuminate\Database\Eloquent\Collection;

class PostAttributes
{
    public function __invoke(PostSerializer $serializer, Post $post): array
    {
        if ($post->relationLoaded('kilowhatRichEmbeds')) {
            /**
             * @var Collection $embeds
             */
            $embeds = $post->kilowhatRichEmbeds;

            $post->setRelation('kilowhatLinkRichEmbeds', $embeds->where('is_link'));
        }

        $attributes = [];

        if ($post->kilowhat_rich_embeds_disable) {
            $attributes['kilowhatRichEmbedsDisable'] = true;
        }

        if ($serializer->getActor()->can('edit', $post)) {
            $canUseRichEmbeds = false; //$serializer->getActor()->hasPermission('kilowhat-rich-embeds.useOnAnyPost'); // TODO: Permission not implemented yet, not really needed?
            if (!$canUseRichEmbeds && $post->user) {
                // We check if the original post author is allowed to use the embeds
                // This will include ourselves
                $canUseRichEmbeds = $post->user->hasPermission('kilowhat-rich-embeds.useOnOwnPost');
            }

            $canDisableRichEmbeds = $serializer->getActor()->hasPermission('kilowhat-rich-embeds.disableOnAnyPost');
            if (!$canDisableRichEmbeds && $post->user) {
                $canDisableRichEmbeds = $post->user->hasPermission('kilowhat-rich-embeds.disableOnOwnPost');
            }

            $attributes['canUseRichEmbeds'] = $canUseRichEmbeds;
            $attributes['canDisableRichEmbeds'] = $canDisableRichEmbeds;
        }

        return $attributes;
    }
}
