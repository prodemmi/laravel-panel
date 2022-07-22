<?php

return [
    // If debug_mode be true, after any error lava will show a dialog for exception error
    'debug_mode'    => true,
    'resource_namespace' => 'App\Dashboard', // TODO
    'user_resource' => UserResource::class, // TODO
    'icon_template' => '<i class="ri-$name-line"></i>', //www.remixicon.com
    'file_manager'  => [
        'disk' => config('filesystems.disks.public')
    ],
    'table'         => [
        'empty' => '...'
    ]
];
