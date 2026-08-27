<?php

namespace Kilowhat\RichEmbeds;

use Psr\Log\LoggerInterface;

trait ExceptionLoggerTrait
{
    protected function logError(string $message, \Exception $exception)
    {
        resolve(LoggerInterface::class)->error('[rich-embeds] ' . $message . ' ' . get_class($exception) . ' ' . $exception->getMessage());
    }
}
