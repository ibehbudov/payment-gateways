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
        'ApproveURL'    =>  '/',
        'CancelURL'     =>  '/',
        'DeclineURL'    =>  '/',
    ]
];
