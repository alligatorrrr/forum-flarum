<?php
session_start(['cookie_lifetime' => 86400]);

$bank   = json_decode(file_get_contents(__DIR__ . '/../../reg.bank.json'), true);   // 从 Json 文件读取题库
$config = require_once __DIR__ . '/../../reg.config.php';                           // 读取配置文件

$correntAnswer = 0;

if (!isset($_SESSION['register.passed'])) die('<script>alert(\'注册失败 请先进行答题\');location.href=\'.\'</script>');  // 正确率不足 答题失败

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $config['site']['app.addr'] . '/api/users');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'data' => [
        'attributes' => [
            'username' => $_POST['username'],
            'nickname' => $_POST['nickname'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'fof_terms_policy_1' => true,                   // 用户使用的插
            'fof_terms_policy_2' => true,                   // 件要求注册时
            'fof_terms_policy_3' => true,
            'fof_terms_policy_4' => true,                   // 需要同意协议
            'fof-doorkey' => $config['doorman.invite.code'] // 输入一个可无限使用的密钥供 API 途径创建的用户注册使用
        ]
    ]
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Token ' . $config['flarum.token'] . '; userId=1',
    'Content-Type: application/json'
]);
$return = json_decode(curl_exec($ch), true);
curl_close($ch);
if (isset($return['errors'])) {
    // var_dump($return);
    die('<script>alert(\'注册失败：邮箱或用户名已被注册或不符合规范\');location.href=\'verify.php\'</script>');
} else {
    unset($_SESSION['register.passed']);
    die('<script>alert(\'注册成功\');location.href=\'/\'</script>');
}
?>