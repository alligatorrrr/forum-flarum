<?php
/*
 * This file is part of xypp/user-decoration.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


namespace Xypp\UserDecoration\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Xypp\UserDecoration\UserOwnDecoration;
use InvalidArgumentException;

class UserOwnDecorationSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'user-own-decoration';
    private UserDecorationSerializer $userDecorationSerializer;
    public function __construct(UserDecorationSerializer $userDecorationSerializer)
    {
        $this->userDecorationSerializer = $userDecorationSerializer;
    }
    /**
     * {@inheritdoc}
     *
     * @param UserOwnDecoration $model
     * @throws InvalidArgumentException
     */
    protected function getDefaultAttributes($model)
    {
        if (!($model instanceof UserOwnDecoration)) {
            throw new InvalidArgumentException(
                get_class($this) . ' can only serialize instances of ' . UserOwnDecoration::class
            );
        }
        return [
            "user_id" => $model->user_id,
            "decoration_id" => $model->decoration_id,
            "type" => $model->type
        ];
    }
}
