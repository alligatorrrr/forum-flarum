<?php
/*
 * This file is part of xypp/acgembed-rr.
 * 
 * Copyright (c) 2024 小鱼飘飘
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace xypp\acgembed\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use xypp\acgembed\MediaInfo;
use InvalidArgumentException;

class MediaInfoSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'media-infos';

    /**
     * {@inheritdoc}
     *
     * @param MediaInfo $model
     * @throws InvalidArgumentException
     */
    protected function getDefaultAttributes($model)
    {
        if (!($model instanceof MediaInfo)) {
            throw new InvalidArgumentException(
                get_class($this) . ' can only serialize instances of ' . MediaInfo::class
            );
        }

        // See https://docs.flarum.org/extend/api.html#serializers for more information.

        return [
            "source" => $model->source,
            "url" => $model->url,
            "title" => $model->title,
            "poster" => $model->poster,
            "desc" => $model->desc,
            "created_at" => $model->created_at,
            "updated_at" => $model->updated_at,
            "deleted_at" => $model->deleted_at,
            "post_id" => $model->post_id
        ];
    }
}
