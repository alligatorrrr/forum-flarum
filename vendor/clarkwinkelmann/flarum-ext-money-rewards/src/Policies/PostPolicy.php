<?php

namespace ClarkWinkelmann\MoneyRewards\Policies;

use Flarum\Post\CommentPost;
use Flarum\Post\Post;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class PostPolicy extends AbstractPolicy
{
    public function rewardWithMoney(User $actor, Post $post)
    {
         $recipient = User::find($post->user_id); // 获取接收打赏的用户
    return ($post instanceof CommentPost) && // 只有文本帖子
        $post->user_id && // 不能打赏匿名帖子或已删除用户的帖子
        $post->user_id !== $actor->id && // 不能打赏自己的帖子
        $actor->can('rewardPostsWithMoney', $post->discussion) && // 发起者需要有打赏的权限
        (null !== $recipient && $recipient->can('rewardPostsWithMoney', $post->discussion)); // 确保接收者有接收打赏的权限
}
 

}