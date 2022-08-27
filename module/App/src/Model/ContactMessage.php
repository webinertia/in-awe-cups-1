<?php

declare(strict_types=1);

namespace App\Model;


class ContactMessage
{
    public $fullName;
    public $email;
    public $message;

    public function exchangeArray(array $data)
    {
        $this->fullName = !empty($data['fullName']) ? $data['fullName'] : null;
        $this->email = !empty($data['email']) ? $data['email'] : null;
        $this->message = !empty($data['message']) ? $data['message'] : null;
    }

    public function toArray()
    {
        return [
            'fullName' => $this->fullName,
            'email' => $this->email,
            'message' => $this->message,
        ];
    }

    public function getArrayCopy()
    {
        return [
            'fullName' => $this->fullName,
            'email' => $this->email,
            'message' => $this->message,
        ];
    }
}
