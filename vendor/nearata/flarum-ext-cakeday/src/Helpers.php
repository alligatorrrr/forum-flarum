<?php

namespace Nearata\CakeDay;

use Flarum\User\User;

class Helpers
{
    public static function canView(User $actor): bool
    {
        /**
         * @var \Flarum\Settings\SettingsRepositoryInterface
         */
        $settings = resolve('flarum.settings');

        return $actor->isAdmin() ||
            $actor->can('nearata-cakeday.can-view-anniversaries-page') &&
            (bool) $settings->get('nearata-cakeday.admin.anniversaries_page');
    }
}
