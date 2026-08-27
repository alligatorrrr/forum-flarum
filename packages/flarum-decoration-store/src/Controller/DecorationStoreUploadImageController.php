<?php
namespace Ziven\decorationStore\Controller;

use Ziven\decorationStore\DecorationStoreUploader;
use Ziven\decorationStore\DecorationStoreValidator;
use Flarum\User\AvatarValidator;

use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use FoF\Upload\Helpers\Util;

class DecorationStoreUploadImageController implements RequestHandlerInterface {
    protected $uploader;
    protected $validator;

    public function __construct(DecorationStoreUploader $uploader, DecorationStoreValidator $validator){
        $this->uploader = $uploader;
        $this->validator = $validator;
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface{
        $request->getAttribute('actor')->assertAdmin();
        $decorationItemImageType = Arr::get($request->getParsedBody(), 'decorationItemImageType');

        if($decorationItemImageType!=="avatarFrame"){

        }

        $file = Arr::get($request->getUploadedFiles(), 'decorationItemImage');
        $this->validator->assertValid(['decorationItemImage' => $file]);

        return new JsonResponse([
            'path' => $this->uploader->upload($file,$decorationItemImageType),
        ]);
    }
}