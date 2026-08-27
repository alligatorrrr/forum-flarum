<?php

namespace Kilowhat\RichEmbeds\Exceptions;

use Flarum\Foundation\ErrorHandling\HandledError;
use Flarum\Locale\Translator;

class BlacklistedUrlHandler
{
    public function handle(BlacklistedUrl $exception): HandledError
    {
        /**
         * @var $translator Translator
         */
        $translator = resolve(Translator::class);

        return (new HandledError($exception, 'blacklisted_url', 422))->withDetails([
            [
                'detail' => $translator->trans('kilowhat-rich-embeds.api.error.blacklistedUrl', [
                    '{url}' => $exception->getUrl(),
                ]),
                'source' => [
                    'parameter' => 'url',
                ],
            ],
        ]);
    }
}
