<?php

namespace Annonny\GptBot\Helper;

use Flarum\Lock\Post\DiscussionLockedPost;
use Flarum\Post\CommentPost;
use Flarum\Post\PostRepository;
use Flarum\Discussion\Command\DeleteDiscussion;
use Flarum\Discussion\DiscussionRepository;
use Flarum\Discussion\Command\SetLocked;
use Flarum\User\UserRepository;
use Flarum\Group\GroupRepository;
use ClarkWinkelmann\MoneyRewards\Controllers\CreateRewardController;
use Laminas\Diactoros\ServerRequest;
use Tobscure\JsonApi\Document;
use Flarum\Foundation\ValidationException;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Extension\ExtensionManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Validation\Factory;
use Flarum\Api\Controller\AbstractCreateController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Flarum\Notification\NotificationSyncer;
use Flarum\Http\RequestUtil;

class FlarumHelper
{
    protected static $botUser;

    protected static function getBotUser()
    {
        if (static::$botUser) return static::$botUser;
        $botUserRepository = app(UserRepository::class);
        static::$botUser = $botUserRepository->findOrFail(101);
        return static::$botUser;
    }

    public static function getRandomEmoticon() {
        $emoticons = [
            '(✿◠‿◠)', 'ლ(╹◡╹ლ)', '(＾◡＾)', '(≧◡≦)', '(づ｡◕‿‿◕｡)づ',
            '(✿ ♥‿♥)', '(づ￣ ³￣)づ', '(╯3╰)', '(´｡• ᵕ •｡`)', '(✿◠‿◠)ﾉ♡',
            '(づ｡◕‿‿◕｡)づ♡', '(ﾉ◕ヮ◕)ﾉ:･ﾟ✧', '(づ｡◕‿‿◕｡)づ･ﾟ:✧', '(✿ ♥‿♥)',
            'ʕ•́ᴥ•̀ʔっ', '(✿◠‿◠)', '(づ｡◕‿‿◕｡)づ', '(✿‿)', '(づ￣ ³￣)づ',
            '(≧◡≦)', '(づ｡◕‿‿◕｡)づ♡', '(ﾉ◕ヮ◕)ﾉ:･ﾟ✧', '(づ｡◕‿‿◕｡)づ･ﾟ:✧',
            '(｡♥‿♥｡)', '(｡･ω･｡)', '(◕‿◕✿)', '(≧ω≦)', '(´ω｀★)',
            '(＾ω＾)', '(ﾉ´ヮ`)ﾉ*: ･ﾟ', '(๑>ᴗ<๑)', '(⁄ ⁄>⁄ ▽ ⁄<⁄ ⁄)', '(ง ื▿ ื)ว',
            '(｡•̀ᴗ-)✧', '(✧ω✧)', '(◕ᴗ◕✿)', '(´｡• ω •｡`)', '(｡•̀ᴗ-)و ̑̑',
            '(*^‿^*)', '(っ˘ω˘ς )', '(⁀ᗢ⁀)', '٩(๑❛ᴗ❛๑)۶',
            '(*^▽^*)', '(•̀ᴗ•́)و ̑̑', '(｡’▽’｡)♡', 'ヽ(＾Д＾)ﾉ', '(*≧▽≦)',
            '(☆▽☆)', '(⌒▽⌒)☆', '(o^▽^o)', '(o･ｪ･o)', '(✪ω✪)',
            '(*ﾟ▽ﾟ*)', '＼(＾▽＾)／', '(･ิω･ิ)', 'ヽ(・∀・)ﾉ', '(/^-^(^ ^*)/',
            '(///ω///)♪','(ฅ•ω•ฅ)♡','(ฅ•ω•ฅ)','♪(๑ᴖ◡ᴖ๑)♪',
        ];

        return $emoticons[array_rand($emoticons)];
    }

    public static function getRandomBadEmoticon() {
        $emoticons = [
            '(´•༝•`)', '(＞﹏＜)', '(;´༎ຶД༎ຶ`)', '(ó﹏ò｡)', '｡゜(｀Д´)゜｡',
            '（>﹏<）', '(´;︵;`)', '(╥_╥)', '(T_T)', '(｡•́︿•̀｡)',
            'o(╥﹏╥)o', '(つ﹏<。)', '(๑꒦ິㅿ꒦ີ)', '(⌯˃̶᷄ ﹏ ˂̶᷄⌯)', '(｡•́︿•̀｡)',
            '(๑´╹‸╹`๑)', '(*´>д<)', '(;*△*;)', '(;°◇°;)', '(´༎ຶོρ༎ຶོ`)'
        ];

        return $emoticons[array_rand($emoticons)];
    }

    public static function deleteDiscussionById($discussionId, $userId)
    {
        $discussionRepository = app(DiscussionRepository::class);
        $discussion = $discussionRepository->findOrFail($discussionId);
        $discussionUserId = $discussion->user_id ? $discussion->user_id : $discussion->anonymous_user_id;
        if ($userId != $discussionUserId) return '只有楼主才能删除此贴mora～'.self::getRandomBadEmoticon();
        $discussion->hide();
        $discussion->save();
        //工作日志
        $content = "MORA的工作日志：\n".self::getRandomEmoticon()."帮忙删了mid" . $discussionId . "的帖子mora～";
        $bot_reply = CommentPost::reply(1, $content, 101, null);
        $bot_reply->save();
        $discussionWork = $bot_reply->discussion;
        $discussionWork->refreshLastPost();
        $discussionWork->refreshCommentCount();
        $discussionWork->refreshParticipantCount();
        $discussionWork->save();
    }

    public static function lockDiscussionByDiscussionId($discussionId)
    {
        $discussionRepository = app(DiscussionRepository::class);
        $discussion = $discussionRepository->findOrFail($discussionId);
        $discussion->is_locked = true;
        $discussion->save();
        $post = DiscussionLockedPost::reply(
            $discussion->id,
            50,
            true
        );

        $post = $discussion->mergePost($post);
        //工作日志
        $content = "MORA的工作日志：\n".self::getRandomEmoticon()."帮忙锁了mid" . $discussionId . "的帖子mora～";
        $bot_reply = CommentPost::reply(1, $content, 101, null);
        $bot_reply->save();
        $discussionWork = $bot_reply->discussion;
        $discussionWork->refreshLastPost();
        $discussionWork->refreshCommentCount();
        $discussionWork->refreshParticipantCount();
        $discussionWork->save();
    }

    public static function removeUserFromGroup($userId, $groupId)
    {
        $userRepository = app(UserRepository::class);
        $user = $userRepository->findOrFail($userId);
        if ($user->groups->contains('id', $groupId)) {
            $user->groups()->detach($groupId);
            return '您已从层岩组中移除mora～'.self::getRandomEmoticon();
        }else{
            return '您并不在层岩组中mora～'.self::getRandomBadEmoticon();
        }
    }

    public static function rewardPost($postId,$display_name)
    {
        // 获取依赖项
        $settings = app(SettingsRepositoryInterface::class);
        $events = app(Dispatcher::class);
        $repository = app(PostRepository::class);
        $validation = app(Factory::class);
        $translator = app(TranslatorInterface::class);
        $notifications = app(NotificationSyncer::class);
        // 实例化 CreateRewardController
        $createRewardController = new CustomCreateRewardController($settings, $events, $repository, $validation, $translator, $notifications);
        // 创建 ServerRequestInterface 实例
        $request = new ServerRequest();
        // 为请求设置属性
        $request = $request->withQueryParams(['id' => $postId]);
        // 生成一个1到100之间的随机数
        $randomNumber = mt_rand(1, 100);
        $reward = 100;// 剩下的概率 (100% - 41% = 59%)
        $reward_prob = '5.9%';
        if ($randomNumber <= 1) { // 百分之1的几率
            $reward = 10000; // 1w
            $reward_prob = '0.1%';
        } elseif ($randomNumber <= 11) { // 百分之10的几率 (1% + 10% = 11%)
            $reward = 1231;
            $reward_prob = '1%';
        } elseif ($randomNumber <= 51) { // 百分之30的几率 (11% + 30% = 41%)
            $reward = 300;
            $reward_prob = '3%';
        }
        $request = $request->withParsedBody([
            'data' => [
                'attributes' => [
                    'amount' => $reward,
                    'comment' => "来自575的奖励(本次概率:".$reward_prob.')', // 评论
                    'createMoney' => true, // 是否创建金钱
                ],
            ],
        ]);
        $request = RequestUtil::withActor($request, self::getBotUser());
        $request = $request->withAttribute('bypassCsrfToken', true); // 添加此行以允许绕过 CSRF 令牌检查
        $document = new Document();
        // 调用 data() 方法
        $response = $createRewardController->publicData($request, $document);
        //工作日志
        $rewardAnnouncements = [
            '{$display_name}为小白狗的反馈获得了{$reward}莓钱钱奖励！575会为你加油的',
            '太棒啦！{$display_name}的小白狗反馈获得了{$reward}莓钱钱奖励！感谢厂妹的热爱和支持呢！',
            '575决定给哥哥一个{$reward}莓钱钱的小惊喜',
            '575决定给{$display_name}一个{$reward}莓钱钱的奖励，因为哥哥的反馈真的很有帮助',
            '恭喜{$display_name}获得{$reward}莓钱钱奖励！感谢哥哥为小白狗付出的努力！mora～',
            '好开心！{$display_name}因为小白狗反馈获得了{$reward}莓钱钱的奖励！感谢你的支持和付出！',
            '恭喜{$display_name}获得{$reward}莓钱钱奖励！',
        ];
        $randomIndex = array_rand($rewardAnnouncements);
        $announcement = str_replace(['{$reward}', '{$display_name}'], [$reward, "「".$display_name."」"], $rewardAnnouncements[$randomIndex]);
        $content = $announcement . self::getRandomEmoticon();
        $bot_reply = CommentPost::reply(47448, $content, 101, null);
        $bot_reply->save();
        $discussionWork = $bot_reply->discussion;
        $discussionWork->refreshLastPost();
        $discussionWork->refreshCommentCount();
        $discussionWork->refreshParticipantCount();
        $discussionWork->save();
    }
}

class CustomCreateRewardController extends CreateRewardController
{
    public function publicData(ServerRequest $request, Document $document)
    {
        return $this->data($request, $document);
    }
}
