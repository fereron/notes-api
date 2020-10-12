<?php

namespace App\DependencyInjection\Uploader;

use Symfony\Component\HttpFoundation\File\Exception\CannotWriteFileException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class NoteImageUploader implements FileUploaderInterface
{
    private string $uploadDirectory;
    private SluggerInterface $slugger;

    public function __construct(string $uploadDirectory, SluggerInterface $slugger)
    {
        $this->uploadDirectory = $uploadDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, ?string $uploadPath = null): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($uploadPath ?: $this->uploadDirectory, $newFilename);
        } catch (FileException $e) {
            throw new CannotWriteFileException();
        }

        return $newFilename;
    }

}