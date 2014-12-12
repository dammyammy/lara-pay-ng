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
        | GTPay by GTBank Settings
        |--------------------------------------------------------------------------
        | https://ibank.gtbank.com/GTPay/Test/mman-tech.html
        | Change to for Testing
        */

        'gtpay'     => array(
            'gtpay_mert_id'          => getenv('MERCHANT_ID'),
            'gtpay_tranx_curr'       => getenv('CURRENCY'),
            'gtpay_gway_first'       => 'no', // yes or no
            'gtpay_gway_name'       =>  null, // webpay or migs or null if no is specified gway_first
            'hashkey'                => getenv('HASH_KEY'),
            'gtpay_tranx_noti_url'   => route('pay'),
            'success_url'            => route('thank_you'),
            'fail_url'               => route('failed'),
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
            'currency'          => getenv('CURRENCY'),
            'hashkey'           => getenv('WEBPAY_HASH_KEY'),
            'site_redirect_url' => route('pay'),
            'success_url'       => route('thank_you'),
            'fail_url'          => route('failed'),
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
            'success_url'       => route('thank_you'),
            'fail_url'          => route('failed'),
            'gatewayUrl'        => 'https://voguepay.com/pay/',

        ),
    ),
);
