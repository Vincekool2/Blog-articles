<?php

namespace App\Service;

class UniqueFilenameGenerator
{
    public function generateUniqueFilename($originalFilename, string $fileExtension): string
    {
        $newFilename = 'image-'.$originalFilename.'-'.uniqid().'.'.$fileExtension;

        return $newFilename;
    }

}