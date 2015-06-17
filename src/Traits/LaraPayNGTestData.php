<?php


namespace LaraPayNG\Traits;
use Illuminate\Http\Request;

trait LaraPayNGTestData {

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

            ];
        }

        if ($type == 'subscription') {
            $transactionData = [
                'recurrent' => true,
                'interval'  => 30,
                'memo'      => 'Membership subscription for music club',
                'total'     => 13000.00,
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
                'price_1'           => 50000, // In Kobo,
                'description_1'     => 'That Aso Oke Mumsi Wants',
                'item_2'            => 'Red Aso Oke',
                'price_2'           => 73000, // In Kobo,
                'description_2'     => 'That Aso Oke Tosin Wants',
                'item_3'            => 'Silver Aso Oke',
                'price_3'           => 90000, // In Kobo,
                'description_3'     => 'That Aso Oke I Want',

                'gtpay_cust_id'     => 'francis@grant.com', // auth()->user()->email
                'gtpay_cust_name'   => 'francis grant', // auth()->user()->name
                'gtpay_tranx_amt'   => 213000, // In Kobo,
                'gtpay_tranx_memo'  => 'Paying for 3 Lovely Aso Oke\'s: Black Aso Oke, Red Aso Oke, Silver Aso Oke'

            ];

            return $transactionData;
        }

        dd('GTPay Doesnot support Recurrent Billing');
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
                'price_1'           => 50000,
                'description_1'     => 'That Aso Oke Mumsi Wants',
                'item_2'            => 'Red Aso Oke',
                'price_2'           => 73000,
                'description_2'     => 'That Aso Oke Tosin Wants',
                'item_3'            => 'Silver Aso Oke',
                'price_3'           => 90000,
                'description_3'     => 'That Aso Oke I Want',

                'gtpay_cust_id'     => 'dami@ogunmoye.com', // auth()->user()->email
                'gtpay_cust_name'   => 'dammyammy', // auth()->user()->name
                'gtpay_tranx_amt'   => 213000 // In Kobo

            ];

            return $transactionData;
        }

        return false;
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
                'price_1'           => 50000,
                'description_1'     => 'That Aso Oke Mumsi Wants',
                'item_2'            => 'Red Aso Oke',
                'price_2'           => 73000,
                'description_2'     => 'That Aso Oke Tosin Wants',
                'item_3'            => 'Silver Aso Oke',
                'price_3'           => 90000,
                'description_3'     => 'That Aso Oke I Want',

                'gtpay_cust_id'     => 'dami@ogunmoye.com', // auth()->user()->email
                'gtpay_cust_name'   => 'dammyammy', // auth()->user()->name
                'gtpay_tranx_amt'   => 213000 // In Kobo

            ];

            return $transactionData;
        }

        return false;
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
                'price_1'           => 50000,
                'description_1'     => 'That Aso Oke Mumsi Wants',
                'item_2'            => 'Red Aso Oke',
                'price_2'           => 73000,
                'description_2'     => 'That Aso Oke Tosin Wants',
                'item_3'            => 'Silver Aso Oke',
                'price_3'           => 90000,
                'description_3'     => 'That Aso Oke I Want',

                'gtpay_cust_id'     => 'dami@ogunmoye.com', // auth()->user()->email
                'gtpay_cust_name'   => 'dammyammy', // auth()->user()->name
                'gtpay_tranx_amt'   => 213000 // In Kobo

            ];

            return $transactionData;
        }

        return false;
    }

}
