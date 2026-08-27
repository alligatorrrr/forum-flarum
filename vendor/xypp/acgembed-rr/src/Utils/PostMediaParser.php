<?php
/*
 * This file is part of xypp/acgembed-rr.
 * 
 * Copyright (c) 2024 小鱼飘飘
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */
namespace xypp\acgembed\Utils;

use \xypp\acgembed\MediaInfo;

class PostMediaParser
{
    public static function processAll($id, $postContentXml, $tags, $hasSaved)
    {
        $current = PostMediaParser::getAllMedia($postContentXml, $tags);
        $idHasDealt = [];
        foreach ($current as $media) {
            $curSaved = $hasSaved->find(function ($item) use ($media) {
                return $item->source == $media['source'] && $item->url == $media['url'];
            });
            if (!$curSaved) {
                $curSaved = new MediaInfo();
                $curSaved->post_id = $id;
                $curSaved->source = "";
                $curSaved->url = "";
            } else {
                $idHasDealt[$curSaved->id] = true;
            }
            if ($curSaved->url != $media['url'] || $curSaved->source != $media['source']) {
                $curSaved->source = $media['source'];
                $curSaved->url = $media['url'];
                MediaInfoFetcher::getMediaInfo($curSaved);
                $curSaved->save();
            }
        }
        foreach ($hasSaved as $media) {
            if (!isset($idHasDealt[$media->id]) || !$idHasDealt[$media->id]) {
                $media->delete();
            }
        }
    }
    public static function getAllMedia(string $postContentXml, $tags)
    {
        $mediaList = [];

        foreach ($tags as $source) {
            if (preg_match_all('/(<' . $source . '[^>]*>[^<]*<URL[^>]*>([^<]*).*<\/' . $source . '>)/i', $postContentXml, $result, PREG_SET_ORDER)) {
                foreach ($result as $media) {
                    array_push($mediaList, [
                        "source" => $source,
                        "url" => html_entity_decode($media[2])
                    ]);
                }
            }
        }
        return $mediaList;
    }
}