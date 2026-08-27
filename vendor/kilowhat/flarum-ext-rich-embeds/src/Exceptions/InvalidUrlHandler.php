<?php

namespace Kilowhat\RichEmbeds\Exceptions;

use Flarum\Foundation\ErrorHandling\HandledError;
use Flarum\Locale\Translator;

class InvalidUrlHandler
{
    public function handle(InvalidUrl $exception): HandledError
    {
        /**
         * @var $translator Translator
         */
        $translator = resolve(Translator::class);

        return (new HandledError($exception, 'invalid_url', 422))->withDetails([
            [
                'detail' => $translator->trans('kilowhat-rich-embeds.api.error.invalidUrl', [
                    '{url}' => $exception->getUrl(),
                ]),
                'source' => [
                    'parameter' => 'url',
                ],
            ],
        ]);
    }
}
