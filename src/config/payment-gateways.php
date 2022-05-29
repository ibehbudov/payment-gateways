<?php

return [
    'kapital'  =>  [
        'merchant'  =>  'E1000010',
        'key'       =>  storage_path('app/certificates/test.key'),
        'ssl_cert'  =>  storage_path('app/certificates/test.crt'),
        'taksit_month'  =>  [
            3,
            6,
            9,
            12,
            18
        ],

        /*
         * set your callback url
         */
        'ApproveURL'    =>  'http://127.0.0.1:8000/callback',
        'CancelURL'     =>  'http://127.0.0.1:8000/callback',
        'DeclineURL'    =>  'http://127.0.0.1:8000/callback',
    ]
];
