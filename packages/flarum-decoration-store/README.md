# Decoration Store 社区装饰店

A [Flarum](http://flarum.org) extension. Add decoration store to the forum where user can purchase avatar frame, profile background, username color etc.  
一个Flarum扩展。加入社区装饰店，用户可以在这里购买头像挂件，用户卡背景，用户名颜色等。  

This extension could be useful for forum that want more interactive elements. With this extension users can customize their appearance to make them look unique in the forum. It may also motivates users to keep active, in order to gain more community money to purchase decorative items.

### Required Extension
- [Money](https://discuss.flarum.org/d/4699-money-extension) by Antoine

### Hightlight Features
- Allow users to purchase avatar frame, profile background and username color to decorate their appearance in the forum. More decorations (such as post decoration) will be added in future update.
- More than 100 ready to use decorative resources.
- Three different types of purchase which are: one time purchase, monthly subscription and yearly subscription.
- Optimized for mobile view.

### Compatibility
- Compatible with [Profile Cover](https://extiverse.com/extension/sycho/flarum-profile-cover) extension. But it will override the user's profile cover if he is using a profile background decorate.
- Compatible with all flarum core extensions.
- Third party extensions need to use the export function `avatarWithFrame` and `usernameWithColor` from this extension to display user's avatar frame and username color.

### Installation
You need subscribe [this extension](https://extiverse.com/extension/ziiven/flarum-decoration-store) at Extiverse. Once subscribed, follow the [instructions](https://extiverse.com/premium/subscriptions) at Extiverse to configure composer, and then run the following command to install this extension.

该扩展为付费扩展，请到[这里](https://extiverse.com/extension/ziiven/flarum-decoration-store)来订阅。
无法在Extiverse上支付的用户，可以加我的QQ 2091714527后通过微信或支付宝来支付。
订阅后，请参照[这篇文章](https://extiverse.com/premium/subscriptions)来设置你的composer，之后执行下面的命令来安装。

Install with composer:

```sh
composer require ziiven/flarum-decoration-store
```

### Extra Configuration
To use the subscription and item discount countdown feature, you need to add cron job to your crontab: 
run `crontab -e` in the server terminal
Then add `* * * * * cd /path-to-your-project && php flarum schedule:run`

### Updating

```sh
composer update ziiven/flarum-decoration-store
php flarum migrate
php flarum assets:publish
php flarum cache:clear
```

### Links
- [Discussion](https://discuss.flarum.org/d/32500-decoration-store)
- [Support](https://ziven.flarum.cloud/d/16-decoration-store)

**Please also check my other works**  😃 
Premium [Decoration Store 社区装饰店](https://discuss.flarum.org/d/32500-decoration-store) 
Premium [Raffle Ticket 刮刮乐](https://discuss.flarum.org/d/32316-raffle-ticket) 
Premium [Pay To See 付费可见内容](https://discuss.flarum.org/d/32052-pay-to-see) 
Free [Daily Check In 每日签到](https://discuss.flarum.org/d/31659-daily-check-in) 
Free [Post Number 帖子楼层](https://discuss.flarum.org/d/31713-post-number) 
Free [View History 浏览历史](https://discuss.flarum.org/d/32062-view-history) 
Free [Money Transfer 社区转账](https://discuss.flarum.org/d/32148-money-transfer) 
Free [Money Leaderboard 社区资金排行榜](https://discuss.flarum.org/d/32259-money-leaderboard)

### ScreenShot

---

###### Store page
This is the page where user can browse your store items.

![image](https://user-images.githubusercontent.com/29644610/222951055-740084f4-5b3e-476d-bd7a-ee3308b8778c.jpg)

----

###### Decoration page
User can "equip" his purchased decoration item in the decoration page.

![image](https://user-images.githubusercontent.com/29644610/222951058-b705f26c-d831-47e1-ac4c-e020f2a55c06.jpg)

----

###### Purchase history page
List all the purchased items with details. User can also unsubscribe/resubscribe items in this page.

![image](https://user-images.githubusercontent.com/29644610/222951059-3dbd9b8a-9fee-47d6-a63e-32e8f2065206.jpg)

----

###### Discussion page
The avatar frame and username color in discussion.

![image](https://user-images.githubusercontent.com/29644610/222951062-3da0c8ed-4184-4405-a50a-be7970070650.jpg)

----

###### User card
This is what the user card looks like.

![image](https://user-images.githubusercontent.com/29644610/222951064-adf8c0ad-0caa-47d1-9eef-4ace3fb4a190.jpg)

----

###### Settings page
You can manage your store items in ACP.

![1678007646799](https://user-images.githubusercontent.com/29644610/222951927-a98cca19-c8b4-4cdc-aca8-7d0cd1af9c3d.jpg)

Build-in images
![image](https://user-images.githubusercontent.com/29644610/222977884-c5fc599e-3d52-49ac-afb8-31c686f3feed.jpg)
