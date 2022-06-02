<?php

declare(strict_types=1);

namespace App\Log;

interface LoggableInterface
{
    /** @param array $data */
    public function save($data);

    /** @param array $data */
    public function update($data);

    public function send();
}
