<?php

return [
    'base_url' => env('TAXPASS_API_URL', 'https://devws.petzent.com/AppServices2022'),
    'timeout' => env('TAXPASS_TIMEOUT', 10),
    'ftp' => [
        'host' => env('TAXPASS_FTP_HOST', 'ftp.taxpass.com'),
        'username' => env('TAXPASS_FTP_USERNAME', 'username'),
        'password' => env('TAXPASS_FTP_PASSWORD', 'password'),
    ],
];
