<?php

namespace Annonny\GptBot\Listeners;

use Annonny\GptBot\Job\SendBotReplyJob;
use Annonny\GptBot\Job\SendBotReplyJob1;
use Illuminate\Contracts\Bus\Dispatcher;
use Flarum\Post\Event\Posted;
use Flarum\Post\CommentPost;
use Illuminate\Contracts\Queue\Queue;

class GiveAnswer
{
    protected $events;
    private $queue;

    public function __construct(Dispatcher $events, Queue $queue)
    {
        $this->events = $events;
        $this->queue = $queue;
    }

    public function postWasPosted(Posted $event)
    {
        // 获取当前发布的帖子
        $post = $event->post;

        // 处理 @575 的提及
        if (strpos($post->content, '@575') !== false || strpos($post->content, '@"网友575"') !== false) {
            $this->handleMention($post, '@575', '网友575', SendBotReplyJob::class, 101);
        }

        // 处理 @2201 的提及
        if (strpos($post->content, '@2201') !== false || strpos($post->content, '@"网友2201"') !== false) {
            $this->handleMention($post, '@2201', '网友2201', SendBotReplyJob1::class, 2201);
        }

        // 反馈奖励帖
        $post_reward_discussion_id = array('52116', '47448', '47446');
        if (in_array($post->discussion_id, $post_reward_discussion_id)) {
            if (trim($post->content) != "") {
                $randomNumber = mt_rand(1, 100);
                if ($randomNumber <= 10) {
                    if (is_null($post->user->id)) {
                        return;
                    }
                    FlarumHelper::rewardPost($post->id, $post->user->display_name);
                    return;
                }
            }
        }
    }

    private function handleMention($post, $mentionTag, $mentionDisplayName, $jobClass, $botId)
    {
        // 获取原帖作者
        $user = $post->user;
        // 获取提醒文本
        $mention = "@\"" . $user->display_name . "\"#p" . $post->id;
        // 处理用户文本
        $question_content = str_replace($mentionTag, "", $post->content);
        $question_content = str_replace('@"' . $mentionDisplayName . '"', '', $question_content);
        $pattern = '/#p?\d+/';
        $replacement = '';
        $question_content = preg_replace($pattern, $replacement, $question_content);
        $pattern = '/:zhongli_discord_\d+:/';
        $question_content = preg_replace($pattern, $replacement, $question_content);
        $question_content = trim($question_content);

        if ($question_content == "") {
            $result = "这好像是一个空白消息，哥哥想说什么？" . array('{zxxp14}', '{MM12}')[rand(0, 1)];
            $content = $mention . " " . $result;
            $bot_reply = CommentPost::reply($post->discussion_id, $content, $botId, null);
            $bot_reply->save();
            $bot_reply->mentionsPosts()->sync($post);
            $discussion = $post->discussion;
            $discussion->refreshLastPost();
            $discussion->refreshCommentCount();
            $discussion->refreshParticipantCount();
            $discussion->save();
        } else if ($post->discussion->is_private) {
            $result = "很抱歉，由于" . $mentionDisplayName . "的回答是按使用量付费的，为了防止恶意利用，" . $mentionDisplayName . "无法在私人聊天中回答问题。请见谅" . array('{zxxp14}', '{MM12}')[rand(0, 1)];
            $content = $mention . " " . $result;
            $bot_reply = CommentPost::reply($post->discussion_id, $content, $botId, null);
            $bot_reply->save();
            $bot_reply->mentionsPosts()->sync($post);
            $discussion = $post->discussion;
            $discussion->refreshLastPost();
            $discussion->refreshCommentCount();
            $discussion->refreshParticipantCount();
            $discussion->save();
        } else {
            // 判断是否在让 MORA 删除帖子等
            $delete_expressions = array("抽奖", "帮我抽奖");
            $pattern_mora = "/\s*575\s*[,，、:!！？?]*/i";
            $modified_question_content = preg_replace($pattern_mora, "", $question_content);

            // 匹配类似 "抽2人" 的指令
            $pattern = "/抽(\d+)人/i";
            if (preg_match($pattern, $modified_question_content, $matches)) {
                $num_winners = (int)$matches[1]; // 提取数字指令中的数字
            } else {
                // 如果没有匹配到数字指令，默认抽取一个回复
                $num_winners = 1;
            }

           if ($modified_question_content == "抽奖" || in_array($modified_question_content, $delete_expressions)) {
                if ($post->user->id != $post->discussion->user_id) {
                    // 不是楼主，返回错误信息
                    $content = "很抱歉，您不是楼主，没有权限这样做";
                    $bot_reply = CommentPost::reply($post->discussion_id, $content, $botId, null);
                    $bot_reply->save();
                    $bot_reply->mentionsPosts()->sync($post);
                    $discussion = $post->discussion;
                    $discussion->refreshLastPost();
                    $discussion->refreshCommentCount();
                    $discussion->refreshParticipantCount();
                    $discussion->save();
                    return;
                } else {
                    $posts = $event->post->discussion->posts;
                    // 移除第一个和最后一个帖子
                    $filteredPosts = $posts->slice(1, max(0, $posts->count() - 2));
                    // 移除机器人账号的帖子
                    $filteredPosts = $filteredPosts->filter(function ($post) use ($botId) {
                        return $post->user->id !== $botId;
                    });
                    // 筛选非楼主的帖子
                    $otherPosts = $filteredPosts->filter(function ($post) use ($event) {
                        return $post->user->id !== $event->post->discussion->user->id;
                    });
                    // 随机选取指定数量的帖子
                    $randomPosts = $otherPosts->random(min($num_winners, $otherPosts->count()));

                    $mention = '';
                    // 收集需要同步的帖子ID
                    $postIdsToSync = [];
                    foreach ($randomPosts as $post) {
                        // 获取帖子的用户ID和 displayname
                        $displayName = $post->user->display_name;
                        $mention = $mention . "@\"" . $displayName . "\"#p" . $post->id . "\n";
                        $postIdsToSync[] = $post->id;
                    }
                    $mention = $mention . "恭喜获奖";
                    $bot_reply = CommentPost::reply($post->discussion_id, $mention, $botId, null);
                    $bot_reply->save();
                    $bot_reply->mentionsPosts()->sync($postIdsToSync);
                    $discussion = $post->discussion;
                    $discussion->refreshLastPost();
                    $discussion->refreshCommentCount();
                    $discussion->refreshParticipantCount();
                    $discussion->save();
                }
            } else {
                $this->queue->push(new $jobClass($post, $question_content, $mention));
                
            }
        }
    }
}