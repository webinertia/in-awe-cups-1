<?php

declare(strict_types=1);

return [
    'Guest' => [
        'id' => '0',
        'role' => 'Guest',
        'inheritsFrom' => null,
        'label' => 'Guest',
    ],
    'Member' => [
        'id' => '1',
        'role' => 'Member',
        'inheritsFrom' => 'Guest',
        'label' => 'Member',
    ],
    'Staff' => [
        'id' => '2',
        'role' => 'Staff',
        'inheritsFrom' => 'Member',
        'label' => 'Staff',
    ],
    'Administrator' => [
        'id' => '3',
        'role' => 'Administrator',
        'inheritsFrom' => 'Staff',
        'label' => 'Administrator',
    ],
    'Super Administrator' => [
        'id' => '4',
        'role' => 'Super Administrator',
        'inheritsFrom' => 'Administrator',
        'label' => 'Super Administrator',
    ],
];
