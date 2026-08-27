<?php

/*
 * This file is part of Stickiest.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Xypp\StickyCard;

use Flarum\Post\AbstractEventPost;
use Flarum\Post\MergeableInterface;
use Flarum\Post\Post;

class PostStickCardEffect extends AbstractEventPost implements MergeableInterface
{
    /**
     * {@inheritdoc}
     */
    public static $type = 'discussionStickyCardUsage';

    /**
     * {@inheritdoc}
     */
    public function saveAfter(Post $previous = null)
    {
        $this->save();
        return $this;
    }

    /**
     * Create a new instance in reply to a discussion.
     *
     * @param int  $discussionId
     * @param int  $userId
     * @param bool $isStickiest
     *
     * @return static
     */
    public static function reply($discussionId, $userId, $effect)
    {
        $post = new static();

        $post->content = static::buildContent($effect);
        $post->created_at = time();
        $post->discussion_id = $discussionId;
        $post->user_id = $userId;

        return $post;
    }

    /**
     * Build the content attribute.
     *
     * @param bool $isStickiest Whether or not the discussion is stickied.
     *
     * @return array
     */
    public static function buildContent($effect)
    {
        return ['takeEffect' => (bool) $effect];
    }
}