<?php

return array(

    'gateways' => array(

        /*
        |--------------------------------------------------------------------------
        | Supported Payment Gateways
        |--------------------------------------------------------------------------
        | We Currently Support The following  GTPay, VoguePay & WebPay
        */

        'driver' => 'gtpay',

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

        'transactionIdPrefix' => 'COMPANY' . time() . 'PRODUCT',

        /*
       |--------------------------------------------------------------------------
       | Routes Related
       |--------------------------------------------------------------------------
       |
       */

        'routes' => array(
            'success_route'            => 'thank_you', // Route::get('thank_you');
            'success_route_name'       => 'thank_you', // Route::get('thank_you', ['as' => 'thank_you']);
            'success_view_name'        => 'frontend.larapay.success', // View::make('frontend.success');

            'failure_route'            => 'failed', // Route::get('failed');
            'failure_route_name'       => 'failed', // Route::get('failed', ['as' => 'failed']);
            'failure_view_name'        => 'frontend.larapay.failed', // View::make('frontend.success');


        ),


        /*
       |--------------------------------------------------------------------------
       | GTPay by GTBank Settings
       |--------------------------------------------------------------------------
       | https://ibank.gtbank.com/GTPay/Test/mman-tech.html
       | https://ibank.gtbank.com/GTPay/Test/TestMerchant.aspx
       | Change to for Testing https://gtweb.gtbank.com/GTPay/Tranx.aspx
       */

        'gtpay'     => array(
            'gtpay_mert_id'          => getenv('GTPAY_MERCHANT_ID'),
            'gtpay_tranx_curr'       => getenv('CURRENCY'),
            'gtpay_no_show_gtbank'   => 'yes',
            'gtpay_gway_first'       => 'no', // yes or no
            'gtpay_gway_name'       =>  null, // webpay or migs or null if no is specified gway_first
            'hashkey'                => getenv('GTPAY_HASH_KEY'),
            'gtpay_tranx_noti_url'   => route('pay') ,
            'gatewayUrl'             => 'https://ibank.gtbank.com/GTPay/Tranx.aspx'

        ),




        /*
        |--------------------------------------------------------------------------
        | WebPay by InterSwitch Settings
        |--------------------------------------------------------------------------
        | https://connect.interswitchng.com/documentation/integration-overview/
        | Change Gateway Url to https://stageserv.interswitchng.com/test_paydirect for Production
        */

        'webpay'     => array(
            'mert_id'           => getenv('WEBPAY_MERCHANT_ID'),
            'currency'          => getenv('CURRENCY'),
            'hashkey'           => getenv('WEBPAY_HASH_KEY'),
            'site_redirect_url' => route('pay'),
            'gatewayUrl'        => 'https://stageserv.interswitchng.com/test_paydirect'
        ),


        /*
        |--------------------------------------------------------------------------
        | VoguePay by VoguePay Nigeria Settings
        |--------------------------------------------------------------------------
        |
        */

        'voguepay'     => array(
            'v_merchant_id'     => '4291-0024965',
            'developer_code'    => '55666',
            'submitButton'      => 'buynow_red.png',
            'store_id'          => '25',
            'notify_url'        => route('notification'),
            'gatewayUrl'        => 'https://voguepay.com/pay/',

        ),
    ),

);
