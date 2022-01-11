<?php

return [

    'go_id'         => 'GO_ID',
    'client_id'     => 'CLIENT_ID',
    'client_secret' => 'CLIENT_SECRET',

    'default_scope' => 'ALL', //GoPay\Definition\TokenScope Constants

    // Map Laravel languages to GoPay\Definition\Language Constants
    'languages'    => [
        'en' => 'ENGLISH',
        'sk' => 'SLOVAK',
        'cs' => 'CZECH'
    ],

    'timeout' => 30,

    'sandbox_url' => 'https://gw.sandbox.gopay.com/api',
    'production_url' => 'https://gate.gopay.cz/api'
];