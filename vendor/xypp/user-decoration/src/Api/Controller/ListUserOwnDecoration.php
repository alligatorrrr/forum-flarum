<?php
/*
 * This file is part of xypp/user-decoration.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


namespace Xypp\UserDecoration\Api\Controller;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tobscure\JsonApi\Document;
use Xypp\UserDecoration\Api\Serializer\UserDecorationSerializer;
use Xypp\UserDecoration\Api\Serializer\UserOwnDecorationSerializer;
use Xypp\UserDecoration\UserDecoration;
use Xypp\UserDecoration\UserDecorationRepository;
use Xypp\UserDecoration\UserOwnDecoration;

class ListUserOwnDecoration extends AbstractListController
{
    public $serializer = UserOwnDecorationSerializer::class;
    protected $settings;
    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    protected function data(Request $request, Document $document)
    {
        $param = $request->getQueryParams();
        $actor = RequestUtil::getActor($request);
        if (!isset($param['id']) || $param['id'] == '') {
            $id = $actor->id;
        } else {
            $id = $param['id'];
            if ($id != $actor->id)
                $actor->assertCan('view-decoration');
        }
        if ($id == $actor->id && $actor->can("user.view-all") && $this->settings->get("xypp-user-decoration.view-all")) {
            $decorations = UserDecoration::all();
            $ret = [];
            foreach ($decorations as $dec) {
                $model = new UserOwnDecoration();
                $model->user_id = $id;
                $model->decoration_id = $dec->id;
                $model->type = $dec->type;
                array_push($ret, $model);
            }
            return $ret;
        }

        $builder = UserOwnDecoration::where("user_id", $id);
        return $builder->get();
    }
}
