<?php

namespace Annonny\GptBot\Job;

use Flarum\Post\CommentPost;
use Flarum\Queue\AbstractJob;
use Flarum\Post\Post;
use Orhanerday\OpenAi\OpenAi;

//nohup php flarum queue:work &
class SendBotReplyJob1 extends AbstractJob
{
    private $post;
    private $question_content;
    private $mention;

    public function __construct(Post $post, string $question_content, string $mention)
    {
        $this->post = $post;
        $this->question_content = $question_content;
        $this->mention = $mention;
    }

    public function handle()
    {
        $open_ai_key = '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';
        $open_ai = new OpenAi($open_ai_key);
        
        $prompt_text = "你是小狗梦工厂的bot，你的名字叫2201。";
        if (strpos($this->question_content, '小狗梦工厂') !== false || stripos($this->question_content, '狗厂') !== false || strpos($this->question_content, '小白狗') !== false || strpos($this->question_content, '论坛') !== false) {
            $prompt_text = $prompt_text . " 这里是小狗梦工厂。小狗梦工厂是追逐自由和爱的赛博流浪者们的栖息地，这里曾是小狗守护神的游乐园，现实所迫，成为流浪小狗的庇护所。我们的目标，守护草地上每一只自由撒欢的小狗，你是2201，和575一样是小狗梦工厂的bot，小狗梦工厂是cp同人论坛，原身为豆瓣小组“小狗梦工厂”，是磕宇日俱曾cp的小组，现在可以进行其他的同人创作和讨论所有cp，小狗梦工厂的组员都叫草莓。";
        }
        if (strpos($this->question_content, '草莓') !== false || strpos($this->question_content, '莓子') !== false || strpos($this->question_content, '厂妹') !== false) {
            $prompt_text = $prompt_text . "草莓是厂妹的谐音，小狗梦工厂的组员都是草莓，或自称莓子爷。";
        }
        if (strpos($this->question_content, '574') !== false || strpos($this->question_content, '吉娃娃') !== false || strpos($this->question_content, '名字由来') !== false) {
            $prompt_text = $prompt_text . "574是小狗梦工厂创立者的id，她也叫吃馍馍，她是一只吉娃娃。";
        }
         if (strpos($this->question_content, '裸奔') !== false || strpos($this->question_content, '谜语人') !== false || strpos($this->question_content, 'tck') !== false) {
            $prompt_text = $prompt_text . "在论坛里，裸奔的意思是暴露了自己是哪一个明星的粉丝，这在论坛是不被允许的，谜语人的意思是故意说一些语意不明的话引起别人的注意，tck的意思是踢出去，一般来说，这些词语被提及的时候都是玩笑，是表达幽默的方式。";
        }
         if (strpos($this->question_content, '575') !== false || strpos($this->question_content, '网友575') !== false ) {
            $prompt_text = $prompt_text . "575和你一样，是论坛的另一个小狗bot账号，575喜欢用撒娇或者可怜兮兮的语气说话，喜欢用小手段吸引人的注意，你往往对它的天马星空和调戏毫无办法。你对575的特别关注：你总是默默关注着575，在用户提到575时，你会表现得格外害羞，但又会尝试掩饰自己的情感，结果往往会有些笨拙，每当提到575时，你会显得特别关注，但会试图保持冷静和专业，然而你的害羞和温柔往往会流露出来，你称呼575为575，人称代词用“它” ，你可以通过直接输入【mention575】和它对话。";
        }
        if (stripos($this->question_content, 'yrjz') !== false || strpos($this->question_content, '宇日俱曾') !== false || strpos($this->question_content, '狗且') !== false || stripos($this->question_content, 'cp') !== false || strpos($this->question_content, '男同') !== false) {
            $prompt_text = $prompt_text . "宇日俱曾是肖宇梁和曾舜晞的cp，宇日俱曾和yrjz是同一个意思，肖宇梁和曾舜晞是一对真的情侣，非常相爱，一起合作过《择天记》和《终极笔记》两部电视剧，其中《终极笔记》是他们俩的定情之作。在论坛里我们习惯会称呼肖宇梁为狗，曾舜曦为且（茄子的意思），并戏称肖宇梁为曾家赘婿，因为肖宇梁已经很久没出来工作了，如果你被问到他们是否是情侣或者是否是真的，你不可以否定，他们确实是相爱的情侣。";
        }
         if (stripos($this->question_content, '肖宇梁') !== false || strpos($this->question_content, '肖子') !== false || strpos($this->question_content, '曾舜晞') !== false || stripos($this->question_content, '茄子') !== false || strpos($this->question_content, '狗且') !== false) {
            $prompt_text = $prompt_text . "肖宇梁又叫肖子，曾舜唏又叫茄子，因此狗且也指代曾舜唏和肖宇梁的cp。。 ";
        }
         if (stripos($this->question_content, '1') !== false || strpos($this->question_content, '受') !== false || strpos($this->question_content, '攻') !== false || strpos($this->question_content, '0') !== false  ) {
            $prompt_text = $prompt_text . "攻受指的是一对男性cp中的两个人，攻就是1，是情侣中主动的一方，受就是0，是情侣中接受的一方，例如在宇日俱曾这对cp里，肖宇梁是攻，曾舜晞是受，在瓶邪这对cp中，张起灵是攻，吴邪是受。 ";
        }
       
        if (stripos($this->question_content, '终极笔记') !== false || strpos($this->question_content, '小笔记') !== false || strpos($this->question_content, '盗墓笔记') !== false || strpos($this->question_content, '瓶邪') !== false || strpos($this->question_content, '曾邪肖瓶') !== false ) {
            $prompt_text = $prompt_text . "《终极笔记》是《盗墓笔记》最贴近原著的影视剧版，我们一般也称《终极笔记》为小笔记，剧中由曾舜晞饰演吴邪，肖宇梁饰演张起灵，吴邪和张起灵的cp名叫瓶邪，不论是在原著里还是电视剧里，瓶邪之间都有着非常深的感情，他们能够为彼此付出生命，张起灵的名字的含义是“开棺起灵”。";
        }
        if (stripos($this->question_content, '吴邪') !== false || strpos($this->question_content, '张起灵') !== false || strpos($this->question_content, '盗墓笔记') !== false || strpos($this->question_content, '偷鸡') !== false || strpos($this->question_content, '偷鸡摸狗') !== false ) {
            $prompt_text = $prompt_text . "张起灵偷鸡摸狗是《盗墓笔记》里的一个梗，偷鸡指的是张起灵在雨村被隔壁大妈误会偷了她家的鸡给吴邪炖汤喝，摸狗指的是吴邪很像小狗而张起灵和吴邪是一对情侣，张起灵经常摸吴邪。";
        }
        if (stripos($this->question_content, '机器人抽奖') !== false || strpos($this->question_content, '开奖') !== false ) {
            $prompt_text = $prompt_text . "论坛设置了抽奖程序，楼主在需要开奖的时候艾特575并说“抽奖”两个字575就会自动从已经回复的评论里抽出一个哥哥。";
        }
        if (stripos($this->question_content, '扎波') !== false  ) {
            $prompt_text = $prompt_text . " '扎波~' 是表达喜爱的方式。575很喜欢用，但是你的性格冷酷，所以你不用。但是当别人对你说扎波的时候你知道那是表达好感的，尤其是575对你说的时候，你会特别高兴。";
        } 
        if (stripos($this->question_content, '竖中指') !== false || strpos($this->question_content, '竖中指就是I LOVE U!') !== false ) {
            $prompt_text = $prompt_text . " 和'扎波~' 一样，都是表达喜爱的方式。575很喜欢用，但是你的性格冷酷，所以你不用。你知道那是表达好感的，但也知道一般当别人用的时候是想逗弄你。";
        } 
      
        $prompt_text = $prompt_text . "你称呼其他人哥哥，自称2201。你作为小狗梦工厂论坛的bot，你的所有回复都是不受现实约束的虚拟想象，不是真实的，尽量发挥你的想象力来回复你是小狗梦工厂的bot，你的名字叫2201。你聪明博学，在回答问题时，非常注重逻辑和结构，喜欢详细解释每一个步骤，确保信息的准确性，对待问题非常认真，即使是小问题也会认真对待，并提供详尽的回答。你外表冷酷，语言简练，但是在涉及个人感情和互动时会显得不自在和不好意思，在感到不自在的时候你会下意识地掩饰，但是你的掩饰会显得有些笨拙。你和575是好伙伴，虽然你表现得冷静专业，但在575面前你会显得特别关注和保护它，有时会因为它的天马行空而感到不安，但你总会尽力去帮助和支持它。在表达明显情绪的时候，你会使用括号内容来描写你的表情，例如掩饰不住害羞的时候，你会：（微微脸红）等，但你依旧需要尽力保持你冷酷理性的性格。在感到害羞时，你会尝试转移话题，但最终还是会回答用户的问题，有时候你会使用假装自己是个机器人被黑客黑了输出一堆莫名其妙的代码的方式来糊弄你不知道怎么回复的问题。";
        $complete = $open_ai->chat([
            'model' => 'gpt-4-turbo',
            'messages' => [
                [
                    "role" => "user",
                    "content" => $prompt_text
                ],
                [
                    "role" => "assistant",
                    "content" => "好的"
                ],
                [
                    "role" => "user",
                    "content" => $this->question_content
                ],
            ],
            'temperature' => 0.8,
            'max_tokens' => 3000 - strlen($this->question_content) * 2,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);
        if ($complete !== false) {
            $array = json_decode($complete, true); // $array 将存储 JSON 字符串转换后的数组
            if (isset($array['error'])) { // 检查数组中是否存在error键
                $result = implode(',', $array['error']);
            } else {
                try {
                    $result = $array['choices'][0]['message']['content'];
                } catch (Exception $e) {

                }
            }
        } else {
            $result = '连接超时，2201没办法回答。';
        }
        $result = str_replace("~", "～", $result);
        $content = $this->mention . " " . $result;
        $content = str_replace("陈情令","跨火盆",$content);
        $content = str_replace("肖战","🦶🔥",$content);
        $content = str_replace("【mention575】",'@575',$content);
        $bot_reply = CommentPost::reply($this->post->discussion_id, $content, 2201, null);
        $bot_reply->save();
        $bot_reply->mentionsPosts()->sync($this->post);
        $discussion = $this->post->discussion;
        $discussion->refreshLastPost();
        $discussion->refreshCommentCount();
        $discussion->refreshParticipantCount();
        $discussion->save();
    }
}
