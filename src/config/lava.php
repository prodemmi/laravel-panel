<?php

use App\Dashboard\UserResource;

return [
    'debug_mode'    => false,
    'user_resource' => UserResource::class,
    'icon_template' => '<i class="ri-$name-line"></i>', //www.remixicon.com
    'file_manager'  => [
        'disk' => config('filesystems.disks.public')
    ],
    'table'         => [
        'empty' => '...'
    ]
];
