<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'assets' => [
            'driver' => 'local',
            'root' => public_path('theme'),
        ],

        'views' => [
            'driver' => 'local',
            'root' => resource_path('views'),
        ],

        'theme' => [
            'driver' => 'local',
            'root' => storage_path('app/theme'),
        ],

        'public_attachment' => [
            'driver' => 'local',    //驱动类型,local=本地,s3=亚马逊,当然可以Stoarge::extend扩展比如七牛类似的第三方服务
            'root'   => public_path('/'),  //文件存储的本地物理根目录, 如果是S3驱动,请设置为null值
            'visibility' => null,   //对外可视,比如亚马逊S3服务,这里就会填写public,一般本地local,填写null即可
            'url_root' => '/',   //项目附件服务器URL根,如果是第三方比如S3等,请在默认项目目录config下,填写补充这个url_root
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

    ],

];
