<?php
namespace Ziven\decorationStore;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\AvatarValidator;
use Illuminate\Validation\Factory;
use Intervention\Image\ImageManager;
use Symfony\Contracts\Translation\TranslatorInterface;
use Psr\Http\Message\UploadedFileInterface;

class DecorationStoreValidator extends AvatarValidator{
    protected $config;

    public function __construct(Factory $validator, TranslatorInterface $translator, ImageManager $imageManager, SettingsRepositoryInterface $config){
        parent::__construct($validator, $translator, $imageManager);
        $this->config = $config;
    }

    protected function getRules(): array{
        return [
            'file' => [
                'required',
                'max:'.$this->getMaxSize(),
            ],
        ];
    }

    public function assertValid(array $attributes){
        $this->laravelValidator = $this->makeValidator($attributes);

        $this->assertFileRequired($attributes['decorationItemImage']);
        $this->assertFileMimes($attributes['decorationItemImage']);
        $this->assertFileSize($attributes['decorationItemImage']);
    }

    protected function getAllowedTypes(){
        return ['png','jpeg','jpg'];
    }

    protected function getMaxSize(){
        return 4096;
    }
}
