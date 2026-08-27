<?php

namespace Kilowhat\RichEmbeds\Exceptions;

use Exception;

class InvalidUrl extends Exception
{
    protected $url;

    public function __construct(string $url)
    {
        parent::__construct();

        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
