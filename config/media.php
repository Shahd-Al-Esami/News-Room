<?php

return [

    'disk' => env('MEDIA_DISK', 'public'),


        'base_folder' => env('MEDIA_BASE_FOLDER', 'uploads'),



        'max_size_kb' => 5120,

        'allowed_mimes' => [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/pdf',
        ],

        'allowed_extensions' => [
            'jpg',
            'jpeg',
            'png',
            'webp',
            'pdf',
        ],

        'disallowed_extensions' => [
            'php',
            'php3',
            'php4',
            'phtml',
            'phar',
            'exe',
            'cmd',
            'svg',
        ],


    'allowed_folders' => [
        'article/{id}/images',
        'user/{id}/avatar',
    ],


];
