<?php

/*
 * This file is part of xypp/user-decoration.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Xypp\UserDecoration;

use Flarum\Api\Controller\ListDiscussionsController;
use Flarum\Api\Controller\ListUsersController;
use Flarum\Api\Controller\ShowDiscussionController;
use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\Extend;
use Flarum\Frontend\Controller;
use Flarum\User\User;
use Flarum\User\UserValidator;
use Xypp\UserDecoration\Api\Controller\AddUserDecoration;
use Xypp\UserDecoration\Api\Controller\AddUserOwnDecoration;
use Xypp\UserDecoration\Api\Controller\DeleteUserDecoration;
use Xypp\UserDecoration\Api\Controller\DeleteUserOwnDecoration;
use Xypp\UserDecoration\Api\Controller\ListUserDecorations;
use Xypp\UserDecoration\Api\Controller\ListUserDecorationWithId;
use Xypp\UserDecoration\Api\Controller\ListUserOwnDecoration;
use Flarum\User\Event\Saving as EventUserSaving;
use Xypp\UserDecoration\Api\Serializer\BasicUserAttributeSerializer;
use Xypp\UserDecoration\Api\Serializer\ForumAttributeSerializer;
use Xypp\UserDecoration\Api\Serializer\UserAttributeSerializer;
use Xypp\UserDecoration\Listener\UserSaving;
use Xypp\UserDecoration\Utils\UserOwnDecorationUtil;

$ret = [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/less/admin.less'),
    (new Extend\Routes('api'))
        ->get("/user_decoration", "user_decoration.list", ListUserDecorationWithId::class)
        ->get("/user_decoration_all", "user_decoration_all.list", ListUserDecorations::class)
        ->post("/user_decoration", "user_decoration.create", AddUserDecoration::class)
        ->get("/user_decoration/{id}/delete", "user_decoration.delete", DeleteUserDecoration::class)
        ->get("/user-own-decoration", "user_own_decoration.list", ListUserOwnDecoration::class)
        ->post("/user-own-decoration", "user_own_decoration.create", AddUserOwnDecoration::class)
        ->get("/user-own-decoration/{id}/delete", "user_own_decoration.delete", DeleteUserOwnDecoration::class)
    ,
    (new Extend\ApiSerializer(BasicUserSerializer::class))
        ->attributes(BasicUserAttributeSerializer::class),
    (new Extend\ApiSerializer(UserSerializer::class))
        ->attributes(UserAttributeSerializer::class),
    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attributes(ForumAttributeSerializer::class),
    (new Extend\Event())
        ->listen(EventUserSaving::class, UserSaving::class),
    new Extend\Locales(__DIR__ . '/locale'),
    (new Extend\Settings())
        ->default('xypp-user-decoration.username_hijack', true)
        ->default('xypp-user-decoration.avatar_hijack', true)
        ->default('xypp-user-decoration.view-all', true)
        ->default('xypp-user-decoration.no_decorate_class_filter', "")
        ->serializeToForum('no_decorate_class_filter', "xypp-user-decoration.no_decorate_class_filter"),
];
if (class_exists("\\Xypp\\Store\\Extend\\StoreItemProvider")) {
    $ret[] = (new \Xypp\Store\Extend\StoreItemProvider())->provide(DecorationStoreProvider::class);
}
return $ret;