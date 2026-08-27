<?php

namespace Ziven\decorationStore;

use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Support\Str;
use Psr\Http\Message\UploadedFileInterface;

class DecorationStoreUploader{
    protected $uploadDir;

    public function __construct(Factory $filesystemFactory){
        $this->uploadDir = $filesystemFactory->disk('ziven-decoration-store');
    }

    public function upload(UploadedFileInterface $file, $decorationItemImageType): string {
        $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $filename = $decorationItemImageType."_".Str::random().'.'.$ext;
        $stream = $file->getStream();
        $stream->rewind();

        $this->uploadDir->put($filename, $stream->getContents());

        return $this->uploadDir->url($filename);
    }

    public function remove(string $filename){
        $fullFilename = $filename;

        if ($this->uploadDir->exists($fullFilename)) {
            $this->uploadDir->delete($fullFilename);
        }
    }
}