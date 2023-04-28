<?php

declare(strict_types=1);

namespace App\Upload;

interface UploadHandlerInterface
{
    public function handleUpload(array $fileData);
    public function handleDelete(array $fileData);
}
