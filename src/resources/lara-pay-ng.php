<?php

return [

    'gateways' => [

        /*
        |--------------------------------------------------------------------------
        | Supported Payment Gateways
        |--------------------------------------------------------------------------
        | We Currently Support The following:  GTPay, VoguePay, SimplePay & CashEnvoy
        */

        'driver' => 'GTPay',

        /*
        |--------------------------------------------------------------------------
        | Testing
        |--------------------------------------------------------------------------
        | If set to false, We would assume Production and route to right Gateway URL
        */
//
//        'testing' => true,

        /*
        |--------------------------------------------------------------------------
        | Unique Transaction ID Prefix
        |--------------------------------------------------------------------------
        | A Unique Transaction ID Needs to be Generated for each Transaction.
        | By Default We Concatenate The Transaction ID Prefix Specified Here and
        | the Product Id for the product/service being sold.
        | eg. COMPANY130003003PRODUCT + 121 = COMPANY130003003PRODUCT121.
        | We Specified a Sensible Default, Change as Appropriate
        |
        |
        |--------------------------------------------------------------------------
        | Unique Transaction Reference Prefix FOR CASHENVOY (ONLY ALPHANUMERIC NO SYMBOLS)
        |--------------------------------------------------------------------------
        | A Unique Transaction Reference Needs to be Generated for each (cashenvoy) Transaction.
        */

        'transactionIdPrefix' => 'REF' . time() . 'TRAN',


        /*
        |--------------------------------------------------------------------------
        | Routes Related
        |--------------------------------------------------------------------------
        |
        */

        'routes' => [
            'success_route'            => 'transaction-successful', // Route::get('thank_you');
            'success_route_name'       => 'transaction-successful', // Route::get('thank_you', ['as' => 'thank_you']);
            'success_view_name'        => 'vendor.lara-pay-ng.successful', // View::make('frontend.success');

            'failure_route'            => 'transaction-failed', // Route::get('failed');
            'failure_route_name'       => 'transaction-failed', // Route::get('failed', ['as' => 'failed']);
            'failure_view_name'        => 'vendor.lara-pay-ng.failed', // View::make('frontend.success');


        ],


        /*
        |--------------------------------------------------------------------------
        | GTPay by GTBank Settings
        |--------------------------------------------------------------------------
        | https://ibank.gtbank.com/GTPay/Test/mman-tech.html
        | https://ibank.gtbank.com/GTPay/Test/TestMerchant.aspx
        | Change to https://gtweb.gtbank.com/GTPay/Tranx.aspx  for Testing
        */

        'gtpay'     => [
            'gtpay_mert_id'          => env('GTPAY_MERCHANT_ID', 'GTBxxxxxxxxxxxx'),
            'gtpay_tranx_curr'       => env('CURRENCY', '₦'),
            'gtpay_no_show_gtbank'   => 'yes',
            'gtpay_gway_first'       => 'no', // yes or no
            'gtpay_gway_name'       =>  null, // webpay, ibank or migs or null if no is specified gway_first
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
            'currency'          => env('CURRENCY', '₦'),
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
            'developer_code'    => env('VOGUEPAY_DEV_CODE', 'demo'),
            'submitButton'      => 'buynow_red.png',
            'store_id'          => env('VOGUEPAY_STORE_ID', '1'),
            'notify_url'        => env('VOGUEPAY_REDIRECT_URL', 'payment-notification'),
            'fail_url'          => env('VOGUEPAY_REDIRECT_URL', 'transaction-failed'),
            'success_url'       => env('VOGUEPAY_REDIRECT_URL', 'transaction-successful'),


            'gatewayUrl'        => 'https://voguepay.com/pay/',

            'table'             => 'voguepay_transactions',

        ],

        /*
        |--------------------------------------------------------------------------
        | SimplePay by SimplePay Nigeria Settings
        |--------------------------------------------------------------------------
        | Change GatewayUrl to https://simplepay4u.com/process.php for Production
        */

        'simplepay'     => [
            'member'            => env('SIMPLEPAY_MERCHANT_ID', 'UG7K3RD046240'),
            'site_logo'         => env('SIMPLEPAY_LOGO_URL', 'http://placehold.it/300/300.png'),
            'submitButton'      => 'simplepaylogo.gif',
            'CMAccountid'       => env('SIMPLEPAY_COMMISION_ID', null),
            'unotify'           => env('SIMPLEPAY_REDIRECT_URL', 'payment-notification'),
            'ucancel'           => env('SIMPLEPAY_REDIRECT_URL', 'transaction-failed'),
            'ureturn'           => env('SIMPLEPAY_REDIRECT_URL', 'transaction-successful'),

            'gatewayUrl'        => 'http://sandbox.simplepay4u.com/process.php',

            'table'             => 'simplepay_transactions',

        ],

        /*
        |--------------------------------------------------------------------------
        | CashEnvoy by CashEnvoy Nigeria Settings
        |--------------------------------------------------------------------------
        | https://www.cashenvoy.com/webservice/ for production
        */

        'cashenvoy'     => [
            'ce_merchantid'     => env('CASHENVOY_MERCHANT_ID', '2403'),
            'ce_key'            => env('CASHENVOY_KEY', 'f65bc0f4e5bc72913701e38223471c71'),
            'ce_notifyurl'      => env('CASHENVOY_REDIRECT_URL', 'payment-notification'),
            'icon'              => 'https://www.cashenvoy.com/images/paybt.jpeg',
            'ce_window'         => 'self', //parent

            'gatewayUrl'        => 'https://www.cashenvoy.com/sandbox/',

            'table'             => 'cashenvoy_transactions',
        ],

    ]

];
