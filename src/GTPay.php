<?php


namespace LaraPayNG;

use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use LaraPayNG\Contracts\PaymentGateway;
use LaraPayNG\Traits\CanGenerateInvoice;

class GTPay extends Helpers implements PaymentGateway
{
    use CanGenerateInvoice;

    /**
     * Define Gateway name
     */
    const GATEWAY = 'GTPay';


    /**
     * @param $key
     *
     * Retrieve A Config Key From GTPay Gateway Array
     *
     * @return mixed
     */
    public function config($key)
    {
        return $this->getConfig(strtolower(self::GATEWAY), $key);
    }

    /**
     * @param string $productId
     * @param array $transactionData
     * @param string $class
     * @param string $buttonTitle
     * @param string $gateway
     *
     * Render Pay Button For Particular Product
     *
     * @throws \LaraPayNG\Exceptions\UnknownPaymentGatewayException
     * @return string
     */
    public function payButton($productId, $transactionData = [], $class = '', $buttonTitle = 'Pay Via GTPay', $gateway = self::GATEWAY)
    {
        return $this->generateSubmitButton($productId, $transactionData, $class, $buttonTitle, $gateway);
    }


    /**
     * Log Transaction
     *
     * @param $transactionData
     *
     * @param null $payer
     *
     * @return string
     */
    public function logTransaction($transactionData, $payer = null)
    {
        $items = $this->serializeItemsToJson($transactionData);

        $total = $this->sumItemPrices($transactionData);

        $gtconfig = 'lara-pay-ng.gateways.gtpay.';

        $valueToInsert = [
            'gtpay_tranx_curr'  => isset($transactionData['gtpay_tranx_curr']) ? $transactionData['gtpay_tranx_curr']
                : config($gtconfig . 'gtpay_tranx_curr'),
            'gtpay_tranx_amt'   => isset($transactionData['gtpay_tranx_amt']) ? $transactionData['gtpay_tranx_amt']/100 : $total/100,
            'gtpay_cust_name'   => isset($transactionData['gtpay_cust_name']) ? $transactionData['gtpay_cust_name'] : null,
            'gtpay_cust_id'     => isset($transactionData['gtpay_cust_id']) ? $transactionData['gtpay_cust_id'] : auth()->user()->id,
            'gtpay_echo_data'   => isset($transactionData['gtpay_echo_data']) ? $transactionData['gtpay_echo_data'] : null,
            'gtpay_tranx_memo'  => isset($transactionData['gtpay_tranx_memo']) ? $transactionData['gtpay_tranx_memo'] : null,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
            'items'             => $items,

        ];

        $id = DB::table(config($gtconfig . 'table'))->insertGetId($valueToInsert);

        $transactionId = isset($transactionData['gtpay_tranx_id'])
            ? $transactionData['gtpay_tranx_id']
            : $this->generateTransactionId($id);

        DB::table(config($gtconfig . 'table'))
            ->where('id', $id)
            ->update(['gtpay_tranx_id'  => $transactionId]);

        return $transactionId;

    }



    /**
     *
     * @return mixed
     */
    public function receiveTransactionResponse($transactionData, $transactionId)
    {
        $gtpayConfig = 'lara-pay-ng.gateways.gtpay.';
        $mertId = config($gtpayConfig . 'gtpay_mert_id');

        $queryString = [
            'tranxid'   => $transactionData['gtpay_tranx_id'],
            'mertid'    => $mertId,
            'amount'    => $transactionData['gtpay_tranx_amt'],
            'hash'      => hash('sha512', $mertId . $transactionData['gtpay_tranx_id'] . config($gtpayConfig . 'hashkey'), false)

//                'tranxid' => 'PLM_1394115494_11180',
//                'mertid' => 212,
//                'amount' => 200000,
//                'hash' =>  'F48289B1C72218C6C02884C26438FA070864B624D1FD82C90F858AF268B2B82F7A3D2311400B29E9B3731068B89EB8007F36B642838C821CAB47D2AAFB5FA0EF',
        ];

        $client = new Client();

        $request = $client->get('https://ibank.gtbank.com/GTPayService/gettransactionstatus.json', [
            'query'     => $queryString,
            'headers'   =>  ['Accept' => 'application/json' ]
        ]);

        $response = $request->getBody();

        $transaction = json_decode($response, true);

        $result = $this->logResponse($transaction + $transactionData);

        return $this->collateResponse($result);

    }

    /**
     * Log Transaction Response
     *
     * @param $transactionData
     *
     * @return
     */
    public function logResponse($transactionData)
    {
        /*You can do anything you want now with the transaction details or the merchant reference.
        You should query your database with the merchant reference and fetch the records you saved for this transaction.
        Then you should compare the $transaction['total'] with the total from your database.*/


        // Save Response to DB (Keep Transaction Detail)
        // Determine If the Transaction Failed Or Succeeded & Redirect As Appropriate
        // If Success, Notify User Via Email Of their Order
        // Notify Admin Of New Order

        $valueToUpdate = [
            "r_gtpay_tranx_id"          => $transactionData["gtpay_tranx_id"],
            "r_gtpay_amount"            => floatval($transactionData["Amount"]),
            "gtpay_merchant_ref"        => $transactionData["MerchantReference"],
            "gtpay_response_code"       => $transactionData["ResponseCode"],
            "gtpay_response_description"=> $transactionData["ResponseDescription"],
            "gtpay_tranx_status_code"   => $transactionData["gtpay_tranx_status_code"],
            "gtpay_tranx_status_msg"    => $transactionData["gtpay_tranx_status_msg"],
        ];

        $table = config('lara-pay-ng.gateways.gtpay.table');

        DB::table($table)
            ->where('gtpay_tranx_id', $transactionData['gtpay_tranx_id'])
            ->update($valueToUpdate);

        return ( DB::table($table)
            ->where('gtpay_tranx_id', $transactionData['gtpay_tranx_id'])
            ->first());
    }

    /**
     * @param $transactionData
     *
     * @return array|string
     */
    public function serializeItemsToJson($transactionData)
    {
        $items = [ ];

        foreach ($transactionData as $key => $value) {
            if (strpos($key, 'item_') === 0) {
                $items[substr($key, 5)]['item'] = $value;
            }

            if (strpos($key, 'price_') === 0) {
                $items[substr($key, 6)]['price'] = $value/ 100;
            }

            if (strpos($key, 'description_') === 0) {
                $items[substr($key, 12)]['description'] = $value;
            }
        }


        if (empty($items)) {
            $items = json_encode([
                1 => [
                    'item' => $transactionData['gtpay_tranx_memo'],
                    'price' => $transactionData['gtpay_tranx_amt'] / 100,
                    'description' => isset($transactionData['gtpay_echo_data'])
                        ? $transactionData['gtpay_echo_data']
                        : 'N/A'
                ]
            ]);

            return $items;
        }

        $items = json_encode($items);

        return $items;
    }

}
