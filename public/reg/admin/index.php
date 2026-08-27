<?php

session_start(['cookie_lifetime' => 86400]);

$bank   = json_decode(file_get_contents(__DIR__ . '/../../../reg.bank.json'), true);     // 读取题库
$config = require_once __DIR__ . '/../../../reg.config.php';                             // 读取配置

if(isset($_SESSION['register.admin.logined']) or @$_GET['key'] == $config['manage.token']){
    $_SESSION['register.admin.logined'] = true;
}else{
    die('<script>alert(\'请先登录\')</script>');
}

?>

<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>题库管理</title>
    <link rel="stylesheet" href="https://cdn.staticfile.org/mdui/1.0.2/css/mdui.min.css">
</head>

<body>
    <div class="mdui-container mdui-typo mdui-p-t-4 mdui-p-b-4">
        <h2>题库管理</h2>
        <form action="save.php" method="POST">
            <div class="question-list">
                <?php
                $qid = 1;
                foreach ($bank as $q => $qInfo) {
                    $options = implode(' ', $qInfo['options']);
                    echo <<<HTML
                    <div>
                        <div class="mdui-textfield">
                            <input class="mdui-textfield-input" type="text" placeholder="问题..." name="question[{$qid}]" value="{$q}" />
                        </div>
                        <div class="mdui-textfield">
                            <textarea class="mdui-textfield-input" type="text" placeholder="选项列表（使用空格隔开）..." name="options[{$qid}]">{$options}</textarea>
                        </div>
                        <div class="mdui-textfield">
                            <input class="mdui-textfield-input" type="text" placeholder="答案..." name="answer[{$qid}]" value="{$qInfo['answer']}" />
                        </div>
                        <div class="mdui-text-right">
                            <button class="mdui-btn" type="button" onclick="mdui.$(this).parent().parent().remove()">删除</button>
                        </div>
                        <hr />
                    </div>
                    HTML;
                    $qid++;
                }
                ?>
            </div>
            <div class="mdui-text-right mdui-m-t-1">
                <button class="mdui-btn" type="button" add>添加问题</button>
                <button class="mdui-btn mdui-color-blue-accent" type="submit">保存</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.staticfile.org/mdui/1.0.2/js/mdui.min.js"></script>
    <script>
        var nextId = <?php echo $qid; ?>;
        var $ = mdui.$;
        $(() => {
            $('button[add]').on('click', () => {
                $('.question-list').append('<div><div class="mdui-textfield"><input class="mdui-textfield-input" type="text" placeholder="问题..." name="question[' + nextId + ']" /></div><div class="mdui-textfield"><textarea class="mdui-textfield-input" type="text" placeholder="选项列表（使用空格隔开）..." name="options[' + nextId + ']"></textarea></div><div class="mdui-textfield"><input class="mdui-textfield-input" type="text" placeholder="答案..." name="answer[' + nextId + ']" /></div><div class="mdui-text-right"><button class="mdui-btn" type="button" onclick="mdui.$(this).parent().parent().remove()">删除</button></div><hr /></div>');
                nextId++;
            });
        });
    </script>
</body>

</html>