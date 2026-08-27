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

use xypp\acgembed\MediaInfo;

class MediaInfoFetcher
{
    private static function bilibiliInfo(MediaInfo $mediaInfo)
    {
        $bvid = substr($mediaInfo->url, stripos($mediaInfo->url, "BV"));
        $ch = curl_init("https://api.bilibili.com/x/web-interface/view?bvid=" . $bvid);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);
        $mediaInfo->title = $data["data"]["title"];
        $mediaInfo->poster = $data["data"]["pic"];
        $mediaInfo->desc = $data["data"]["desc"];
    }
    public static function neteastInfo(MediaInfo $mediaInfo)
    {
        if (!preg_match('/[\&\?]id=(\d+)/i', $mediaInfo->url, $result))
            return;
        $id = $result[1];
        if (!preg_match('/(song|album|playlist)/i', $mediaInfo->url, $result))
            return;
        $type = $result[0];

        $ch = curl_init("https://music.163.com/" . $type . "?id=" . $id);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.75 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $p1 = strpos($result, '<script type="application/ld+json">') + strlen('<script type="application/ld+json">');
        $p2 = strpos($result, '</script>', $p1);
        $jsonStr = substr($result, $p1, $p2 - $p1);
        $json = json_decode($jsonStr, true);
        $mediaInfo->title = $json['title'];
        $mediaInfo->desc = $json['description'];
        $mediaInfo->poster = $json['images'][0];
    }
    public static function acfunInfo(MediaInfo $mediaInfo)
    {
        if (!preg_match('/\/v\/(ac[-0-9]+)/i', $mediaInfo->url, $result))
            return;
        $id = $result[1];
        $ch = curl_init("https://www.acfun.cn/v/" . $id);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.75 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = str_ireplace([" ", "\n", "\r"], "", $result);
        $p1 = strpos($result, 'window.pageInfo=window.videoInfo=') + strlen('window.pageInfo=window.videoInfo=');
        $p2 = strpos($result, ';window.videoResource', $p1);
        $jsonStr = substr($result, $p1, $p2 - $p1);
        $json = json_decode($jsonStr, true);
        $mediaInfo->title = $json['title'];
        $mediaInfo->desc = $json['description'];
        $mediaInfo->poster = $json['coverUrl'];
    }
    public static function getMediaInfo(MediaInfo $mediaInfo)
    {
        if ($mediaInfo->source == "bilibili") {
            MediaInfoFetcher::bilibiliInfo($mediaInfo);
        }

        if ($mediaInfo->source == "music163") {
            MediaInfoFetcher::neteastInfo($mediaInfo);
        }

        if ($mediaInfo->source == "acfun") {
            MediaInfoFetcher::acfunInfo($mediaInfo);
        }
    }
}