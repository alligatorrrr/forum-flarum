<?php

namespace ZerosOnesFun\Audio\Upload;

use Flarum\Foundation\AbstractServiceProvider;
use FoF\Upload\Contracts\Template;
use FoF\Upload\File;
use FoF\Upload\Helpers\Util;

class HTMLAudioTemplate implements Template
{
    public function tag(): string
    {
        return 'html-audio';
    }

    public function name(): string
    {
        return 'Audio [audio]';
    }

    public function description(): string
    {
        return 'Audio template for Audio BBCode extension.';
    }

    public function preview(File $file): string
    {
        return '[audio mp3='. $file->url .']';
    }
}

class AudioServiceProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->make(Util::class)->addRenderTemplate($this->container->make(HTMLAudioTemplate::class));
    }
}