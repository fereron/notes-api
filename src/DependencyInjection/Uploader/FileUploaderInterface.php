<?php

namespace App\DependencyInjection\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileUploaderInterface {

    public function upload(UploadedFile $file, ?string $uploadPath = null): string;

}