<?php

return [

    'gateways' => [

        /*
        |--------------------------------------------------------------------------
        | Supported Payment Gateways
        |--------------------------------------------------------------------------
        | We Currently Support The following  GTPay, VoguePay & WebPay
        */

        'driver' => 'voguepay',

        /*
        |--------------------------------------------------------------------------
        | Unique Transaction ID Prefix
        |--------------------------------------------------------------------------
        | A Unique Transaction ID Needs to be Generated for each Transaction.
        | By Default We Concatenate The Transaction ID Prefix Specified Here and
        | the Product Id for the product/service being sold.
        | eg. COMPANY130003003PRODUCT + 121 = COMPANY130003003PRODUCT121.
        | We Specified a Sensible Default, Change as Appropriate
        */

        'transactionIdPrefix' => 'COMPANY-' . time() . '-PRODUCT-',

        /*
        |--------------------------------------------------------------------------
        | Unique Merchant Reference  Prefix
        |--------------------------------------------------------------------------
        | A Unique Merchant Reference Needs to be Generated for each (VoguePay) Transaction.
        |
        */

        'MerchantReferencePrefix' => 'COMPANY-REF-' . time() . '-',


        /*
        |--------------------------------------------------------------------------
        | Routes Related
        |--------------------------------------------------------------------------
        |
        */

        'routes' => [
            'success_route'            => 'transaction-successful', // Route::get('thank_you');
            'success_route_name'       => 'transaction-successful', // Route::get('thank_you', ['as' => 'thank_you']);
            'success_view_name'        => 'payment.successful', // View::make('frontend.success');

            'failure_route'            => 'transaction-failed', // Route::get('failed');
            'failure_route_name'       => 'transaction-failed', // Route::get('failed', ['as' => 'failed']);
            'failure_view_name'        => 'payment.failed', // View::make('frontend.success');


        ],


        /*
        |--------------------------------------------------------------------------
        | GTPay by GTBank Settings
        |--------------------------------------------------------------------------
        | https://ibank.gtbank.com/GTPay/Test/mman-tech.html
        | https://ibank.gtbank.com/GTPay/Test/TestMerchant.aspx
        | Change to for Testing https://gtweb.gtbank.com/GTPay/Tranx.aspx
        */

        'gtpay'     => [
            'gtpay_mert_id'          => env('GTPAY_MERCHANT_ID', 'GTBxxxxxxxxxxxx'),
            'gtpay_tranx_curr'       => env('CURRENCY', '566'),
            'gtpay_no_show_gtbank'   => 'yes',
            'gtpay_gway_first'       => 'no', // yes or no
            'gtpay_gway_name'       =>  null, // webpay or migs or null if no is specified gway_first
            'hashkey'                => env('GTPAY_HASH_KEY', 'Your Insanely Long HashKey from GTB'),
            'gtpay_tranx_noti_url'   => env('GTPAY_REDIRECT_URL', 'payment-notification'),
            'gatewayUrl'             => 'https://ibank.gtbank.com/GTPay/Tranx.aspx',

            'table'                 => 'gtpay_transactions'

        ],




        /*
        |--------------------------------------------------------------------------
        | WebPay by InterSwitch Settings
        |--------------------------------------------------------------------------
        | https://connect.interswitchng.com/documentation/integration-overview/
        | Change Gateway Url to https://stageserv.interswitchng.com/test_paydirect for Production
        */

        'webpay'     => [
            'mert_id'           => env('WEBPAY_MERCHANT_ID', 'xxxxx'),
            'currency'          => env('CURRENCY', '566'),
            'hashkey'           => env('WEBPAY_HASH_KEY', 'Your Insanely Long HashKey from Interswitch'),
            'site_redirect_url' => env('WEBPAY_REDIRECT_URL', 'payment-notification'),
            'gatewayUrl'        => 'https://stageserv.interswitchng.com/test_paydirect',

            'table'                 => 'webpay_transactions',
        ],


        /*
        |--------------------------------------------------------------------------
        | VoguePay by VoguePay Nigeria Settings
        |--------------------------------------------------------------------------
        |
        */

        'voguepay'     => [
            'v_merchant_id'     => env('VOGUEPAY_MERCHANT_ID', 'demo'),
            'developer_code'    => env('VOGUEPAY_DEV_CODE','demo'),
            'submitButton'      => 'buynow_red.png',
            'store_id'          => env('VOGUEPAY_STORE_ID','1'),
            'notify_url'        => env('VOGUEPAY_REDIRECT_URL', 'payment-notification'),
            'fail_url'          => env('VOGUEPAY_REDIRECT_URL', 'transaction-failed'),
            'success_url'       => env('VOGUEPAY_REDIRECT_URL', 'transaction-successful'),


            'gatewayUrl'        => 'https://voguepay.com/pay/',

            'table'             => 'voguepay_transactions',

        ]

    ]
];
