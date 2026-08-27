<?php

session_start(['cookie_lifetime' => 86400]);

if(isset($_SESSION['register.admin.logined'])){
    $_SESSION['register.admin.logined'] = true;
}else{
    die('<script>alert(\'请先登录\')</script>');
}

$question = [];

foreach ($_POST['question'] as $qid => $q) {
    if (array_search($_POST['answer'][$qid], explode(' ', $_POST['options'][$qid])) === false) {
        echo '题目 ' . $qid . ' 的答案不在选项中<br />';
        continue;
    }

    if (isset($_POST['options'][$qid]) and isset($_POST['answer'][$qid])) {
        $question[$q] = [
            'options' => explode(' ', $_POST['options'][$qid]),
            'answer' => $_POST['answer'][$qid]
        ];
    } else {
        echo '题目 ' . $qid . ' 缺少答案或选项<br />';
    }
}

file_put_contents(__DIR__ . '/../../../reg.bank.json', json_encode($question, JSON_UNESCAPED_UNICODE));

echo '题目已保存';
