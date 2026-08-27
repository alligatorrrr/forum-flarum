<?php

use Flarum\Extension\Exception\MissingDependenciesException;
use Flarum\Extension\Extension;
use Flarum\Extension\ExtensionManager;
use Flarum\Foundation\Config;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;

return [
    'up' => function (Builder $schema) {
        $config = resolve(Config::class);

        if (Arr::get($config, 'kilowhat.ignore-mysql-requirement')) {
            return;
        }

        $version = $schema->getConnection()->selectOne('select version() as version')->version;

        if (preg_match('~^([0-9]+)\.([0-9]+)\.([0-9]+)[-.$]~', $version, $matches) !== 1) {
            // If the version format doesn't match our expectations, don't try anything
            return;
        }

        $incompatible = false;

        if ((int)$matches[1] === 5) {
            if ((int)$matches[2] < 7 || ((int)$matches[2] === 7 && (int)$matches[3] < 8)) {
                $incompatible = true;
            }
        } else if ((int)$matches[1] === 10) {
            if ((int)$matches[2] < 2 || ((int)$matches[2] === 2 && (int)$matches[3] < 7)) {
                $incompatible = true;
            }
        }

        if (!$incompatible) {
            return;
        }

        $required = 'MySQL 5.7.8+ or MariaDB 10.2.7+';

        $logger = resolve(LoggerInterface::class);

        // Since the exception thrown below won't be logged, we should still log something in the file since that's where we ask people to look
        $logger->error('kilowhat-rich-embeds: Migrations aborted. Your MySQL version appears to be unsupported. Version found: ' . $version . '. Version required: ' . $required);

        $manager = resolve(ExtensionManager::class);

        // MissingDependenciesException is the only kind of exception that will be visible on the page when enabling the extension
        // At that time we have no way to load custom javascript nor custom error handlers
        throw new MissingDependenciesException(
            $manager->getExtension('kilowhat-rich-embeds'),
            [
                // Create a fake extension with name MySQL that will be retrieved through ExtensionManager::pluckTitles by MissingDependenciesExceptionHandler
                new Extension(__DIR__, [
                    'name' => 'not-an-actual-composer-package/mysql',
                    'extra' => [
                        'flarum-extension' => [
                            'title' => $required,
                        ],
                    ],
                ]),
            ]
        );
    },
    'down' => function (Builder $schema) {
        // Nothing to do
    },
];
