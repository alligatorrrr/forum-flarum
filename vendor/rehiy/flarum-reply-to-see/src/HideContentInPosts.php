<?php

namespace Rehiy\ReplyToSee;

use Flarum\Api\Serializer\BasicPostSerializer;
use Flarum\Database\AbstractModel;
use Flarum\Post\Post;   // ← 必須加這行

class HideContentInPosts extends FormatContent
{
    public function __invoke(BasicPostSerializer $serializer, AbstractModel $post, array $attributes)
    {
        if (empty($attributes['contentHtml'])) {
            return $attributes;
        }

        $newHTML = $attributes['contentHtml'];
        if (!str_contains($newHTML, '<reply2see>')) {
            return $attributes;
        }

        $actor   = $serializer->getActor();
        $disc    = $post['discussion'] ?? null;
        $replied = false;

        if ($disc && !$actor->isGuest()) {
        $actorId = (int) $actor->id;

    // 在本讨论里，存在“属于我”的评论即可（未隐藏）
    // Clark 匿名：真实作者在 posts.anonymous_user_id
    $replied = Post::query()
        ->where('discussion_id', $disc->id)
        ->where('type', 'comment')
        ->whereNull('hidden_at')
        ->where(function ($q) use ($actorId) {
            $q->where('user_id', $actorId)
              ->orWhere('anonymous_user_id', $actorId);
        })
        ->exists();
}


        if ($replied || $serializer->getActor()->isAdmin()) {
            $newHTML = preg_replace(
                '/<reply2see>(.*?)<\/reply2see>/is',
                '<div class="reply2see"><div class="reply2see_title">' .
                $this->translator->trans('rehiy-reply-to-see.forum.hidden_content') .
                '</div>$1</div>',
                $newHTML
            );
        } else {
            $newHTML = preg_replace(
                '/<reply2see>(.*?)<\/reply2see>/is',
                '<div class="reply2see"><div class="reply2see_alert">' .
                $this->translator->trans(
                    'rehiy-reply-to-see.forum.reply_to_see',
                    ['{reply}' => '<a class="reply2see_reply">' . $this->translator->trans('core.forum.discussion_controls.reply_button') . '</a>']
                ) . '</div></div>',
                $newHTML
            );
        }

        $attributes['contentHtml'] = $newHTML;
        return $attributes;
    }
}
