<?php


namespace LaraPayNG;

use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use LaraPayNG\Contracts\PaymentGateway;
use LaraPayNG\Traits\CanGenerateInvoice;

class VoguePay extends Helpers implements PaymentGateway
{
    use CanGenerateInvoice;

    /**
     * Define Gateway name
     */
    const GATEWAY = 'VoguePay';

    /**
     * Log Transaction
     *
     * @param $transactionData
     * @param null $payerId
     *
     * @return array
     */
    public function logTransaction($transactionData, $payerId = null)
    {
        $items = $this->serializeItemsToJson($transactionData);

        $total = $this->sumItemPrices($transactionData);

        $valueToInsert = [
            'total'         => isset($transactionData['total']) ? $transactionData['total']: $total,
            'items'         => $items,
            'memo'          => isset($transactionData['memo']) ? $transactionData['memo'] : null,
            'store_id'      => isset($transactionData['store_id']) ? $transactionData['store_id']
                : config('lara-pay-ng.gateways.voguepay.store_id'),
            'recurrent'     => isset($transactionData['recurrent']) ? $transactionData['recurrent'] : false,
            'interval'      => isset($transactionData['interval']) ? $transactionData['interval'] : null,
            'payer_id'      => is_null($payerId) ? $payerId : null,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ];

        $transactionId = DB::table(config('lara-pay-ng.gateways.voguepay.table'))->insertGetId($valueToInsert);

        $merchantRef = isset($transactionData['merchant_ref'])
            ? $transactionData['merchant_ref']
            : $this->generateMerchantReference($transactionId);

        DB::table(config('lara-pay-ng.gateways.voguepay.table'))
            ->where('id', $transactionId)
            ->update(['merchant_ref'  => $merchantRef]);

        return $merchantRef;
    }


    /**
     * @param string $transactionId
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
    public function payButton($transactionId, $transactionData = [], $class = '', $buttonTitle = 'Pay Now', $gateway = self::GATEWAY)
    {
        return $this->generateSubmitButton($transactionId, $transactionData, $class, $buttonTitle, $gateway);
    }

    /**
     * @param $transactionId
     *
     * @return mixed|void
     * @internal param $transactionData
     *
     */
    public function receiveTransactionResponse($transactionId, $mertId)
    {
        $queryString = config('lara-pay-ng.gateways.voguepay.v_merchant_id') == 'demo'
            ?   [
                'v_transaction_id' => $transactionId['transaction_id'],
                'type' => 'json',
                'demo' => 'true'
            ]
            :   [
                'v_transaction_id' => $transactionId['transaction_id'],
                'type' => 'json'
            ];

        $client = new Client();

        $request = $client->get('https://voguepay.com/', [
            'query'     => $queryString,
            'headers'   =>  ['Accept' => 'application/json' ]
        ]);

        $response = $request->getBody();

        $transaction = json_decode($response, true);

        $transaction['merchant_ref'] = ($transaction['merchant_ref'] != '') ? $transaction['merchant_ref'] : $mertId;

        $result = $this->logResponse($transaction);

        return $this->collateResponse($result);
    }


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
            "v_transaction_id"  => $transactionData["transaction_id"],
            "v_email"           => $transactionData["email"],
            "v_total"           => floatval($transactionData["total"]),
            "memo"              => $transactionData["memo"],
            "status"            => $transactionData["status"],
            "paid_at"           => $transactionData["date"],
            "v_pay_method"      => $transactionData["method"],
            "referrer"          => $transactionData["referrer"],
            "v_total_credited"  => floatval($transactionData["total_credited_to_merchant"]),
            "v_extra_charges"   => floatval($transactionData["extra_charges_by_merchant"]),
            "v_merchant_charges"=> floatval($transactionData["charges_paid_by_merchant"]),
            "v_fund_maturity"   => $transactionData["fund_maturity"],
            "v_total_paid"      => ($transactionData["status"] == 'Approved') ? floatval($transactionData["total_paid_by_buyer"]) : 0.00,
            "v_process_duration"=> floatval($transactionData["process_duration"])

        ];



        DB::table(config('lara-pay-ng.gateways.voguepay.table'))
            ->where('merchant_ref', $transactionData['merchant_ref'])
            ->update($valueToUpdate);

        return DB::table(config('lara-pay-ng.gateways.voguepay.table'))
            ->where('merchant_ref', $transactionData['merchant_ref'])
            ->first();
    }

    /**
     * @param $key
     *
     * Retrieve A Config Key From VoguePay Gateway Array
     *
     * @return mixed
     */
    public function config($key)
    {
        return $this->getConfig(strtolower(self::GATEWAY), $key);
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
                $items[substr($key, 6)]['price'] = $value;
            }

            if (strpos($key, 'description_') === 0) {
                $items[substr($key, 12)]['description'] = $value;
            }
        }


        if (empty($items)) {
            $items = json_encode([
                1 => [
                    'item' => $transactionData['memo'],
                    'price' => $transactionData['total'],
                    'description' => isset($transactionData['description'])
                        ? $transactionData['description']
                        : 'Billed Every ' . $transactionData['interval'] . ' days'
                ]
            ]);

            return $items;
        }

        $items = json_encode($items);

        return $items;
    }



}
