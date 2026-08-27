<?php

namespace Blomstra\Realtime\Websocket\Concerns;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Container\Container;

trait Settings
{
    protected function flarumConfig(): array
    {
        return resolve('flarum.config') ?? [];
    }

    protected function flarumSettings(): ?SettingsRepositoryInterface
    {
        if (resolve(Container::class)->bound(SettingsRepositoryInterface::class)) {
            return resolve(SettingsRepositoryInterface::class);
        }

        return null;
    }
}
