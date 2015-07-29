<?php

namespace LaraPayNG\Traits;

use Illuminate\Http\Request;

trait LaraPayNGTestData
{
    /**
     * @param Request $request
     *
     * @return array
     */
    private function voguePayTestData(Request $request)
    {
        $type = $request->get('type');

        if ($type == 'products') {
            $transactionData = [
                'item_1'        => 'Black Aso Oke',
                'price_1'       => 500.00,
                'description_1' => 'That Aso Oke Mumsi Wants',
                'item_2'        => 'Red Aso Oke',
                'price_2'       => 730.00,
                'description_2' => 'That Aso Oke Tosin Wants',
                'item_3'        => 'Silver Aso Oke',
                'price_3'       => 900.00,
                'description_3' => 'That Aso Oke I Want',

                'total'     => 2130.00, // Optional, the System will add up prices if value is not present.

            ];
        }

        if ($type == 'subscription') {
            $transactionData = [
                'recurrent' => true,
                'interval'  => 30,
                'memo'      => 'Membership subscription for music club',
                'total'     => 13000.00, // Compulsory as there are no Items, Just a subscription.
            ];

            return $transactionData;
        }

        return $transactionData;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function gtPayTestData($request)
    {
        $type = $request->get('type');

        if ($type == 'products') {
            $transactionData = [
                'item_1'            => 'Black Aso Oke',
                'price_1'           => 500.00,
                'description_1'     => 'That Aso Oke Mumsi Wants',
                'item_2'            => 'Red Aso Oke',
                'price_2'           => 730.00,
                'description_2'     => 'That Aso Oke Tosin Wants',
                'item_3'            => 'Silver Aso Oke',
                'price_3'           => 900.00,
                'description_3'     => 'That Aso Oke I Want',

                'gtpay_cust_id'     => 'francis@grant.com', // auth()->user()->email
                'gtpay_cust_name'   => 'francis grant', // auth()->user()->name
//                'gtpay_tranx_amt'   => 2130.00, // Optional, the System will add up prices if value is not present
                'gtpay_tranx_memo'  => 'Paying for 3 Lovely Aso Oke\'s: Black Aso Oke, Red Aso Oke, Silver Aso Oke',

            ];

            return $transactionData;
        }

        dd('GTPay Does not support Recurrent Billing');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function webPayTestData($request)
    {
        $type = $request->get('type');

        if ($type == 'products') {
            $transactionData = [
                'item_1'            => 'Black Aso Oke',
                'price_1'           => 500.00,
                'description_1'     => 'That Aso Oke Mumsi Wants',
                'item_2'            => 'Red Aso Oke',
                'price_2'           => 730.00,
                'description_2'     => 'That Aso Oke Tosin Wants',
                'item_3'            => 'Silver Aso Oke',
                'price_3'           => 900.00,
                'description_3'     => 'That Aso Oke I Want',

                'webpay_cust_id'     => 'francis@grant.com', // auth()->user()->email
                'webpay_cust_name'   => 'francis grant', // auth()->user()->name
//                'webpay_tranx_amt'   => 2130.00, // Optional, the System will add up prices if value is not present
                'webpay_tranx_memo'  => 'Paying for 3 Lovely Aso Oke\'s: Black Aso Oke, Red Aso Oke, Silver Aso Oke',

            ];

            return $transactionData;
        }

        dd('WebPay Does not support Recurrent Billing');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function simplePayTestData($request)
    {
        $type = $request->get('type');

        if ($type == 'products') {
            $transactionData = [
                'item_1'            => 'Black Aso Oke',
                'price_1'           => 500.00,
                'description_1'     => 'That Aso Oke Mumsi Wants',
                'item_2'            => 'Red Aso Oke',
                'price_2'           => 730.00,
                'description_2'     => 'That Aso Oke Tosin Wants',
                'item_3'            => 'Silver Aso Oke',
                'price_3'           => 900.00,
                'description_3'     => 'That Aso Oke I Want',

                'action'        => 'payment',
                'comments'      => 'payment for ASo Oke',
                'quantity'      => 3,
                'escrow'        => false,
                'freeclient'    => false,
                'nocards'       => false,
                'giftcards'     => true,
                'chargeforcard' => true,
                'setup'         => 0,
                'tax'           => 0,
                'shipping'      => 0,

                'payer_id'     => 'dami@ogunmoye.com', // auth()->user()->email
                'price'        => 2130.00, // In Kobo

            ];

            return $transactionData;
        }

        if ($type == 'subscription') {
            $transactionData = [
                'period' => 30, // in days
                'trial'  => 7, // in days
                'action' => 'subscription',

                'comments' => 'payment for ASo Oke',
                'product'  => 'payment for ASo Oke',

                'payer_id'     => 'dami@ogunmoye.com', // auth()->user()->email
                'price'        => 2130.00, // In Kobo

            ];

            return $transactionData;
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function cashEnvoyTestData($request)
    {
        $type = $request->get('type');

        if ($type == 'products') {
            $transactionData = [
                'item_1'            => 'Black Aso Oke',
                'price_1'           => 500.00,
                'description_1'     => 'That Aso Oke Mumsi Wants',
                'item_2'            => 'Red Aso Oke',
                'price_2'           => 730.00,
                'description_2'     => 'That Aso Oke Tosin Wants',
                'item_3'            => 'Silver Aso Oke',
                'price_3'           => 900.00,
                'description_3'     => 'That Aso Oke I Want',

                'ce_customerid'     => 'francis@grant.com', // auth()->user()->email
                'ce_memo'           => 'Paying for 3 Lovely Aso Oke\'s: Black Aso Oke, Red Aso Oke, Silver Aso Oke', // auth()->user()->name
//                'ce_amount'         => 2130.00 // Optional, the System will add up prices if value is not present

            ];

            return $transactionData;
        }

        dd('CashEnvoy Does not support Recurrent Billing');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function allInOneTestData($request)
    {
        $type = $request->get('type');

        if ($type == 'products') {
            $transactionData = [
                'item_1'            => 'Black Aso Oke',
                'price_1'           => 500.00,
                'description_1'     => 'That Aso Oke Mumsi Wants',
                'item_2'            => 'Red Aso Oke',
                'price_2'           => 730.00,
                'description_2'     => 'That Aso Oke Tosin Wants',
                'item_3'            => 'Silver Aso Oke',
                'price_3'           => 900.00,
                'description_3'     => 'That Aso Oke I Want',

                // Cash Envoy
                'ce_customerid'     => 'francis@grant.com', // auth()->user()->email
                'ce_memo'           => 'Paying for 3 Lovely Aso Oke\'s: Black Aso Oke, Red Aso Oke, Silver Aso Oke', // auth()->user()->name
                'ce_amount'         => 2130.00, // Optional, the System will add up prices if value is not present

                // Simple Pay
                'action'        => 'payment',
                'comments'      => 'payment for ASo Oke',
                'quantity'      => 3,
                'escrow'        => false,
                'freeclient'    => false,
                'nocards'       => false,
                'giftcards'     => true,
                'chargeforcard' => true,
                'setup'         => 0,
                'tax'           => 0,
                'shipping'      => 0,
                'payer_id'      => 'dami@ogunmoye.com', // auth()->user()->email
                'price'         => 2130.00, // In Kobo

                // GTPay
                'gtpay_cust_id'     => 'francis@grant.com', // auth()->user()->email
                'gtpay_cust_name'   => 'francis grant', // auth()->user()->name
                'gtpay_tranx_amt'   => 2130.00, // Optional, the System will add up prices if value is not present
                'gtpay_tranx_memo'  => 'Paying for 3 Lovely Aso Oke\'s: Black Aso Oke, Red Aso Oke, Silver Aso Oke',

                // VoguePay
                'total'     => 2130.00,
            ];

            return $transactionData;
        }

        if ($type == 'subscription') {
            $transactionData = [
                // Simple Pay
                'period' => 30, // in days
                'trial'  => 7, // in days
                'action' => 'subscription',

                'comments' => 'payment for ASo Oke',
                'product'  => 'payment for ASo Oke',

                'payer_id'     => 'dami@ogunmoye.com', // auth()->user()->email
                'price'        => 2130.00,

                // Vogue Pay
                'recurrent' => true,
                'interval'  => 30,
                'memo'      => 'Membership subscription for music club',
                'total'     => 13000.00, // Compulsory as there are no Items, Just a subscription.

            ];

            return $transactionData;
        }

        dd('Not All Gateways support Recurrent Billing');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function testTransactionData(Request $request)
    {
        $defaultDriver = strtolower(config('lara-pay-ng.gateways.driver'));

        switch ($defaultDriver) {
            case 'voguepay':
                $transactionData = $this->voguePayTestData($request);
                break;

            case 'gtpay':
                $transactionData = $this->gtPayTestData($request);
                break;

            case 'simplepay':
                $transactionData = $this->simplePayTestData($request);
                break;

            case 'cashenvoy':
                $transactionData = $this->cashEnvoyTestData($request);
                break;

            case 'webpay':
                $transactionData = $this->webPayTestData($request);
                break;

            case 'default':
                dd('Check Config');
                break;
        }

        return $transactionData;
    }
}
