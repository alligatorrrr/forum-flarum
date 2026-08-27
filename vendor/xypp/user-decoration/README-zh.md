# User Decoration

![Title Poster](https://ice.frostsky.com/2024/07/11/13334249c820695eb905dcfeecc5a32f.png)

![License](https://img.shields.io/badge/license-MIT-blue.svg)
[![Latest Stable Version](https://img.shields.io/packagist/v/xypp/user-decoration.svg)](https://packagist.org/packages/xypp/user-decoration)
[![Total Downloads](https://img.shields.io/packagist/dt/xypp/user-decoration.svg)](https://packagist.org/packages/xypp/user-decoration)

一个 [Flarum](http://flarum.org) 插件. 为用户的 头像/用户名/帖子/卡片 添加装扮.

> 实际上，本插件一个更加合适的名字可能是“基于用户的样式注入引擎”

## Notice

此代码使用了一些激进的技巧来拦截所有头像/用户名. 这可能造成其他部分的插件出现问题. 如果出现问题, 请在设置中禁用头像/用户名劫持.

该拓展目前不支持任何使用了highlight辅助函数的拓展。目前我们支持了flarum/core和flarum/mentions。如果发现某些地方用户名显示为空，请关闭用户头像/用户名劫持功能并提交报告。

为了体验完整功能, 你可能需要安装 [xypp/store](https://github.com/zxy19/store)

## Introduction

本插件提供了一种可以使用 CSS 和其他特殊标记来改变用户组件的方法。

管理员可以创建装扮，然后将其授予用户。用户在个人主页可以看到已经获得的装扮，并运用。装扮理论上生效与论坛的任何地方。

如果安装了[xypp/store](https://github.com/zxy19/store)，用户可以从商店购买装扮

## Usage

### 添加装扮

使用管理员账号登录，导航到用户页，点击装扮按钮打开装扮页面。点击右上角的添加装扮按钮即可。

### 授予装扮

导航到用户页，点击右上角的控制按钮，点击“授予装扮”即可。

### 创建商品

导航到商店页面，点击右上角的添加商品按钮。

填写完基本信息后，选择商品提供者为`装扮`

待加载完成后，在下方选择要作为商品的装扮。

如果希望装扮有时间限制，可以直接填写`时间限制`。

如非必要请不要填写使用次数限制，这不会产生影响，但是会导致用户从已购中使用时扣除次数。

## Special CSS Mark

使用.base 来选中装扮的容器元素（即.decoration-container 元素）。该元素会拥有原先的对应元素的所有属性并包装其内容。

使用.element-aNameToBeFillThere[...]来创建一个新的元素。具体用例见下

```css
/** 规则 */
.element-<unique-id>[tag="<tag>"][parent="<parent selector>"][copy="<copy selector>"][after="<after selector>"][content="<content>"][class="<class>"]

.element-test
/** 创建一个内容为Hello的span元素，位于container下 */
.element-test1[tag="span"][content="hello"] {
  ....;
}
/** 创建一个div元素，位于.Post-body下 */
.element-test1[tag="div"][parent="Post-body"] {
  ....;
}
/** 创建一个内容为Hello的span元素，位于第一个元素的第一个子元素下 */
.element-test1[tag="div"][parent="* *"] {
  ....;
}
/** 复制第一个img元素，位于container下 */
.element-test1[tag="#"][content="img"] {
  ....;
}
```

上文中的`<parent selector>` 和 `<copy selector>` 支持使用 `*` 匹配任意元素,或者使用字符串直接匹配 className 或者 tagName。注意 className 和 tagName 不需要加`.`或者`#`前缀，同时区分大小写。多级匹配之间用空格分割。

## Q&A

- 为什么样式选择不适用 QuerySelector？

> 元素匹配不运行在 DOM 上，而是运行在 VNode 树上。

- 管理员无法删除自己的装扮？

> 关闭`查看所有`功能

- 装扮不生效

> 打开控制台查看是否输出了警告或错误。

## Installation

Install with composer:

```sh
composer require xypp/user-decoration:"*"
```

## Updating

```sh
composer update xypp/user-decoration:"*"
php flarum migrate
php flarum cache:clear
```

## Links

- [Packagist](https://packagist.org/packages/xypp/user-decoration)
- [GitHub](https://github.com/zxy19/user-decoration)
