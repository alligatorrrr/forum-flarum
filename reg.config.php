<?php

return [
    'site' => [
        'title' => '小狗梦工厂',
        'app.addr' => 'https://www.xn--fmrv2rkpbv8uymm.com/'
    ],  // 为了使用方便以及性能没有使用数据库 故需手动填写一些 flarum 信息
    'question.count' => 8,                  // 每次注册从题库中抽取的题目数量
    'question.correct.required' => 8,       // 注册要求答对的题目数量
    'doorman.invite.code' => '3JBWWXC2',    // 填写一个可无限使用的 Doorman 邀请码
    'manage.token' => 'cI0eD0uF3cA7eH1b',   // 管理控制台密钥
    'flarum.token' => 'cD3iD1eF1fH0jH0b',   // Flarum API 密钥
];
