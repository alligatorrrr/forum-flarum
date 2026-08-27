<?php
session_start(['cookie_lifetime' => 86400]);

$bank   = json_decode(file_get_contents(__DIR__ . '/../../reg.bank.json'), true);   // 从 Json 文件读取题库
$config = require_once __DIR__ . '/../../reg.config.php';                           // 读取配置文件

// if (@$_SESSION['register.question']) {
// 若用户获取题目后刷新 则获取的题目不变
//     $list = $_SESSION['register.question'];
// } else {
// 用户首次获取题库 写入 Session
$list = $_SESSION['register.question'] = array_rand($bank, $config['question.count']);  //// 猜错需求了
// }
?>

<!doctype html>
<html lang="zh-cn">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title><?php echo $config['site']['title']; ?> - 注册答题</title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="images/android-desktop.png">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Material Design Lite">
    <link rel="apple-touch-icon-precomposed" href="images/ios-desktop.png">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#3372DF">

    <link rel="shortcut icon" href="images/favicon.png">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.staticfile.org/material-design-lite/1.3.0/material.blue-light_blue.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="mdl-demo mdl-color--grey-100 mdl-color-text--grey-700 mdl-base">
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header mdl-layout__header--scroll mdl-color--primary">
            <div class="mdl-layout--large-screen-only mdl-layout__header-row">
            </div>
            <div class="mdl-layout--large-screen-only mdl-layout__header-row">
                <h3><?php echo $config['site']['title']; ?></h3>
            </div>
            <div class="mdl-layout--large-screen-only mdl-layout__header-row">
            </div>
            <div class="mdl-layout__tab-bar mdl-js-ripple-effect mdl-color--primary-dark">
                <a href="/" class="mdl-layout__tab">首页</a>
                <a href="#" class="mdl-layout__tab is-active">注册答题</a>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="mdl-layout__tab-panel is-active" id="overview">
                <section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
                    <form class="mdl-card mdl-cell mdl-cell--12-col" action="verify.php" method="POST">
                        <div class="mdl-card__supporting-text mdl-grid mdl-grid--no-spacing">
                            <h4 class="mdl-cell mdl-cell--12-col">注册答题</h4>
                            <p>欢迎参加 <b><?php echo $config['site']['title']; ?></b> 注册答题，请回答下面的 <b><?php echo $config['question.count']; ?></b> 个小题并至少答对 <b><?php echo $config['question.correct.required']; ?></b> 题方可完成注册。</p>
                            <div class="mdl-grid">
                                <?php
                                // 遍历获取到的题目列表生成前端组件
                                foreach ($list as $qid => $q) {
                                    $qInfo = $bank[$q];    // 获取题目信息
                                    echo <<<HTML
                                    <div class="mdl-cell mdl-cell--12-col">{$q}</div>
                                    HTML;
                                    foreach ($qInfo['options'] as $option) {
                                        echo <<<HTML
                                        <div class="mdl-cell mdl-cell--3-col mdl-cell--3-col-phone">
                                            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect">
                                                <input type="radio" class="mdl-radio__button" name="q-{$q}" value="{$option}" required>
                                                <span class="mdl-radio__label">$option</span>
                                            </label>
                                        </div>
                                        HTML;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="mdl-card__actions">
                            <button tyle="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-button--accent">提交</button>
                        </div>
                    </form>
                </section>
            </div>
            <footer class="mdl-mega-footer">
                <div class="mdl-mega-footer--bottom-section">
                    <div class="mdl-logo">
                        <?php echo $config['site']['title']; ?>
                    </div>
                    <ul class="mdl-mega-footer--link-list">
                        <li><a href="/">主页</a></li>
                    </ul>
                </div>
            </footer>
        </main>
    </div>
    <script src="https://cdn.staticfile.org/material-design-lite/1.3.0/material.min.js"></script>
</body>

</html>