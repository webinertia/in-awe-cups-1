<?php

declare(strict_types=1);

namespace App\Model;

class ContactMessage
{
    /** @var string $fullName */
    public $fullName;
    /** @var string $email */
    public $email;
    /** @var string $message */
    public $message;

    public function exchangeArray(array $data)
    {
        $this->fullName = ! empty($data['fullName']) ? $data['fullName'] : null;
        $this->email    = ! empty($data['email']) ? $data['email'] : null;
        $this->message  = ! empty($data['message']) ? $data['message'] : null;
    }

    public function toArray(): array
    {
        return [
            'fullName' => $this->fullName,
            'email'    => $this->email,
            'message'  => $this->message,
        ];
    }

    public function getArrayCopy(): array
    {
        return [
            'fullName' => $this->fullName,
            'email'    => $this->email,
            'message'  => $this->message,
        ];
    }
}
