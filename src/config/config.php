<?php

return array(

    'gateways' => array(

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

        'transactionIdPrefix' => 'COMPANY' . time() . 'PRODUCT',

        /*
        |--------------------------------------------------------------------------
        | GTPay by GTBank Settings
        |--------------------------------------------------------------------------
        | https://ibank.gtbank.com/GTPay/Test/mman-tech.html
        | Change to for Testing
        */

        'gtpay'     => array(
            'mert_id'          => getenv('MERCHANT_ID'),
            'tranx_curr'       => getenv('CURRENCY'),
            'hashkey'          => getenv('HASH_KEY'),
            'tranx_noti_url'   => route('pay'),
            'gatewayUrl'       => 'https://ibank.gtbank.com/GTPay/Tranx.aspx'

        ),

        /*
        |--------------------------------------------------------------------------
        | WebPay by InterSwitch Settings
        |--------------------------------------------------------------------------
        | https://connect.interswitchng.com/documentation/integration-overview/
        | Change Gateway Url to https://stageserv.interswitchng.com/test_paydirect/pay for testing
        */

        'webpay'     => array(
            'currency'          => getenv('CURRENCY'),
            'hashkey'           => getenv('WEBPAY_HASH_KEY'),
            'site_redirect_url' => route('pay'),
            'gatewayUrl'        => 'https://stageserv.interswitchng.com/test_paydirect/pay'
        ),

        /*
        |--------------------------------------------------------------------------
        | VoguePay by VoguePay Nigeria Settings
        |--------------------------------------------------------------------------
        |
        */

        'voguepay'     => array(
            'currency'          => getenv('CURRENCY'),
            'hashkey'           => getenv('WEBPAY_HASH_KEY'),
            'site_redirect_url' => route('pay'),
            'submitButton'      => 'https://voguepay.com/images/buttons/buynow_red.png',
            'gatewayUrl'        => 'https://voguepay.com/pay/'
        ),
    ),
);
