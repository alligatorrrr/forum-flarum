# ACGEMBED-RR

本插件为 zequeen 的 acgembed 插件的分支
本插件在原插件的基础上添加了点击播放按钮，希望有效缓解大量播放器出现时页面的渲染压力。
插件同时为Bilibili,AcFun,网易云音乐添加了信息爬虫

### 安装

```
composer require xypp/acgembed-rr
```

### 更新

```
composer update xypp/acgembed-rr
php flarum migrate
php flarum cache:clear
```

### 旧版说明

> 这是原 zequeen 作者仓库的内容，该仓库已经被删除。

本插件对 [lcinhk/flarum-ext-acgembed](https://github.com/LCinHK/flarum-ext-acgembed) 修改后的

1. 调整版本号使其可以安装在正式版以后
2. 调整部分嵌入摆件的大小，以适配文字总宽度
3. 不知道了

### LCinHK版本说明

具体使用参考 [这里](https://discuss.flarum.org.cn/d/1559) 
仓库 [LCinHK/flarum-ext-acgembed](https://github.com/LCinHK/flarum-ext-acgembed)