<?php
/*
 * This file is part of xypp/acgembed-rr.
 * 
 * Copyright (c) 2024 小鱼飘飘
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace xypp\acgembed\Listener;

use Flarum\Formatter\Formatter;
use Flarum\Post\Event\Posted;
use Flarum\Post\Event\Saving;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use xypp\acgembed\MediaInfo;
use xypp\acgembed\Utils\PostMediaParser;
use xypp\acgembed\Utils\MediaInfoFetcher;

class PostSaving
{
    protected $parser;
    protected $settings;
    public function __construct(
        Formatter $parser,
        SettingsRepositoryInterface $settings
    ) {
        $this->parser = $parser;
        $this->settings = $settings;
    }

    public function handle(Saving $event)
    {
        if (!$this->settings->get('xypp.acgembed-rr.fetch')) {
            return;
        }
        $post = $event->post;
        $user = $event->actor;
        if (!$post instanceof Post) {
            return;
        }
        if (!isset($post->content) || !is_string($post->content)) {
            return;
        }
        if (!$post->id) {
            return;
        }
        $contentXml = $this->parser->parse($post->content);
        $hasSaved = MediaInfo::where('post_id', $post->id)->get();
        $tags = $this->settings->get('xypp.acgembed-rr.tags');
        $tagList = explode(",", $tags);
        PostMediaParser::processAll($post->id, $contentXml, $tagList, $hasSaved);
    }
}
