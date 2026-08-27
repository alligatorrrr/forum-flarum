<?php
session_start(['cookie_lifetime' => 86400]);

$bank   = json_decode(file_get_contents(__DIR__ . '/../../reg.bank.json'), true);   // 从 Json 文件读取题库
$config = require_once __DIR__ . '/../../reg.config.php';                           // 读取配置文件

if (!isset($_SESSION['register.passed']) or count($_POST) != 0) {
    // 注册失败的用户 返回再次填写信息 不验证正确率

    $correntAnswer = 0;

    // 从 Session 获取列表并遍历 防止非正常用户漏题
    foreach ($_SESSION['register.question'] as $q) {
        if (!isset($_POST['q-' . $q])) die('<script>alert(\'请完整回答题目\');location.href=\'.\'</script>'); // 出现漏题
        if ($bank[$q]['answer'] == $_POST['q-' . $q]) $correntAnswer++;                                      // 答案正确
    }

    if ($correntAnswer < $config['question.correct.required']) die('<script>alert(\'准确率未达标 请重新答题\');location.href=\'.\'</script>');  // 正确率不足 答题失败
}

// 通过答题
$_SESSION['register.passed'] = 1;
?>

<!doctype html>
<html lang="zh-cn">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title><?php echo $config['site']['title']; ?> - 注册账号</title>

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
                <a href="#" class="mdl-layout__tab is-active">注册账号</a>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="mdl-layout__tab-panel is-active" id="overview">
                <section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
                    <form class="mdl-card mdl-cell mdl-cell--12-col" action="reg.php" method="POST">
                        <div class="mdl-card__supporting-text mdl-grid mdl-grid--no-spacing">
                            <h4 class="mdl-cell mdl-cell--12-col">注册答题</h4>
                            <p>恭喜您通过答题，接下来请注册账号。
                            如注册后弹出邮箱或用户名不符合规范，（可能的原因：1.昵称或用户名过短/长，需要在3个字以上20个字以内。2.昵称或用户名和他人重复了。）如一直无法注册成功，可以私信微博：村口大喇叭开始播报 或者邮件询问。</p>
                            <div class="mdl-grid">
                                <div class="mdl-cell mdl-cell--12-col">
                                    请输入一个用户名（可以由英文、数字和划线组成，注册后将不能更改，具有唯一性）
                                    <br />
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="text" name="username" type="text" required>
                                        <label class="mdl-textfield__label" for="username">您的用户名...</label>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--12-col">
                                    请输入一个昵称（支持中文及其他语言字符，支持emoji，昵称长度3-20个字，注册后可以更改，具有唯一性）
                                    <br />
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="text" name="nickname" type="text" required>
                                        <label class="mdl-textfield__label" for="nickname">您的昵称...</label>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--12-col">
                                    请输入您的邮箱（需要使用邮箱激活账号、登录账号以及找回密码，请务必填写正确的邮箱）
                                    <br />
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="text" name="email" type="email" required>
                                        <label class="mdl-textfield__label" for="email">您的电子邮箱...</label>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--12-col">
                                    请输入您的密码（至少8位）
                                    <br />
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="password" name="password" type="password" required>
                                        <label class="mdl-textfield__label" for="password">您的密码...</label>
                                    </div>
                                </div>
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
