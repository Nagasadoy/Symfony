<?php

namespace App\Services;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedHelper
{
    public function __construct(private readonly string $uploadedPath)
    {
    }

    public function uploadArticleImage(UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadedPath . '/public/uploads';

        $originalFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = Urlizer::urlize($originalFileName) . uniqid('', true) .
            '.' . $uploadedFile->guessExtension();

        $uploadedFile->move(
            $destination,
            $newFileName
        );

        return $newFileName;
    }
}