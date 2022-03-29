<?php

declare(strict_types=1);

namespace Uploader\Adapter;

interface AdapterInterface
{
    const EVENT_UPLOAD = 'upload';
    public function upload();
}
