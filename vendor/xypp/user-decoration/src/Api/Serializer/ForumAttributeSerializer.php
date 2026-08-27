<?php
/*
 * This file is part of xypp/user-decoration.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


namespace Xypp\UserDecoration\Api\Serializer;

use Flarum\Settings\SettingsRepositoryInterface;

class ForumAttributeSerializer
{
    protected $settings;
    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function __invoke($serializer, $user, $attributes)
    {
        $attributes['username_hijack'] = !!($this->settings->get('xypp-user-decoration.username_hijack', true));
        $attributes['avatar_hijack'] = !!($this->settings->get('xypp-user-decoration.avatar_hijack', true));
        return $attributes;
    }
}