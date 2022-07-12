<?php

declare(strict_types=1);

return [
    'guest' => [
        'id' => '0',
        'role' => 'guest',
        'inheritsFrom' => null,
        'label' => 'Guest',
    ],
    'user' => [
        'id' => '1',
        'role' => 'user',
        'inheritsFrom' => 'guest',
        'label' => 'User',
    ],
    'staff' => [
        'id' => '2',
        'role' => 'staff',
        'inheritsFrom' => 'user',
        'label' => 'Staff',
    ],
    'admin' => [
        'id' => '3',
        'role' => 'admin',
        'inheritsFrom' => 'staff',
        'label' => 'Administrator',
    ],
    'superAdmin' => [
        'id' => '4',
        'role' => 'superAdmin',
        'inheritsFrom' => 'admin',
        'label' => 'Super Administrator',
    ],
];
