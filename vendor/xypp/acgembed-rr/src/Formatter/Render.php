<?php
/*
 * This file is part of xypp/acgembed-rr.
 * 
 * Copyright (c) 2024 小鱼飘飘
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace xypp\acgembed\Formatter;

use Flarum\Settings\SettingsRepositoryInterface;
use s9e\TextFormatter\Renderer;

class Render
{
    protected $settings;
    public function __construct(
        SettingsRepositoryInterface $settings
    ) {
        $this->settings = $settings;
    }

    public function __invoke(Renderer $renderer, $context, string $xml)
    {
        if (!$this->settings->get('xypp.acgembed-rr.lazy') && !$this->settings->get('xypp.acgembed-rr.show-url')) {
            return $xml;
        }
        $tags = $this->settings->get('xypp.acgembed-rr.tags');
        $tagList = explode(",", $tags);
        foreach ($tagList as $tag) {
            $ttag = $tag;
            if ($this->settings->get('xypp.acgembed-rr.lazy')) {
                $ttag = '<TEMPLATE>$1<URL url="$2">' . $ttag . '</URL></TEMPLATE>';
            }
            if ($this->settings->get('xypp.acgembed-rr.show-url')) {
                $ttag .= '<URL url="$2">$2</URL>';
            }
            $xml = preg_replace('/(<' . $tag . '[^>]*>[^<]*<URL[^>]*>([^<]*).*<\/' . $tag . '>)/i', $ttag, $xml);
        }
        return $xml;
    }
}
