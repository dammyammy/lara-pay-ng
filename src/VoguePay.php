<?php


namespace LaraPayNG;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class VoguePay extends Helpers implements PaymentGateway {

    /**
     * Define Gateway name
     */
    const GATEWAY = 'VoguePay';


    /**
     * Log Transaction
     *
     * @param $transactionData
     *
     */
    public function logTransaction($transactionData)
    {

//        return true;

        $items = $this->serializeItemsToJson($transactionData);

        $transactionId = DB::table(config('lara-pay-ng.gateways.voguepay.table'))->insertGetId([
            'transaction_id' => $transactionData['transaction_id'],
            'merchant_ref' => $transactionData['merchant_ref'],
            'total' => $transactionData['total'],
            'items' => $items,
            'store_id' => isset($transactionData['store_id']) ? $transactionData['store_id']
                : config('lara-pay-ng.gateways.voguepay.store_id'),
            'recurrent' => isset($transactionData['recurrent']) ? $transactionData['recurrent']
                : false,
            'interval' => isset($transactionData['interval']) ? $transactionData['interval']
                : null,

            'payer_id' => isset($transactionData['payer_id']) ? $transactionData['payer_id']
                : null,
        ]);

        return $transactionId;


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
        return $this->generateSubmitButton($transactionId, $transactionData, $class, $buttonTitle, $gateway );
    }

    /**
     * @param $transactionData
     *
     * @return mixed|void
     */
    public function sendTransactionToGateway($transactionData)
    {
        $id = $this->logTransaction($transactionData);

        $newdata = [];

        foreach($transactionData as $key => $data)
        {
            if($data['gatewayUrl'] !== $data[$key])
            {
                $newdata[$key] = $data;
            }
        }

        $newdata['merchant_ref'] = $id;

        $client = new Client();

        $response = $client->post($transactionData['gatewayUrl'], [
            'body'     =>  $newdata,
        ]);

        dd($response);



//        $response = $request->getBody();

        // Save Response to DB (Keep Transaction Detail)
        // Determine If the Transaction Failed Or Succeeded & Redirect As Appropriate
        // If Success, Notify User Via Email Of their Order
        // Notify Admin Of New Orders

    }

    /**
     * @param $transactionId
     *
     * @return mixed|void
     * @internal param $transactionData
     *
     */
    public function receiveTransactionResponse($transactionId)
    {



        //        $table->string('merchant_ref');
//        $table->string('transaction_id');
//        $table->float('total');
//        $table->json('items');
//        $table->string('store_id')->nullable();
//        $table->string('recurrent')->nullable();
//        $table->integer('interval')->nullable();
//        $table->string('email')->nullable();
//        $table->text('memo')->nullable();
//        $table->float('received_total')->nullable();
//        $table->string('referrer')->nullable();
//        $table->string('method')->nullable();
//        $table->timestamp('paid_at')->nullable();

        if (config('lara-pay-ng.gateways.voguepay.v_merchant_id') == 'demo') {
            $queryString = [
                'v_transaction_id' => $transactionId['transaction_id'],
                'type'             => 'json',
                'demo'             => 'true'
            ];
        }
        else
        {
            $queryString = [
                'v_transaction_id' => $transactionId['transaction_id'],
                'type'             => 'json'
            ];
        }

        $client = new Client();

        $request = $client->get('https://voguepay.com/', [
            'query'     => $queryString,
            'headers'   =>  ['Accept' => 'application/json' ]
        ]);

        $response = $request->getBody();

        $transaction = json_decode($response, true);

        $this->logResponse($transaction);
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

        //        if($transaction['total'] == 0)die('Invalid total'); // Throw Exceptions Later
//        if($transaction['status'] != 'Approved')die('Failed transaction'); // Throw Exceptions Later

//        $transaction['merchant_id'],
//        $transaction['transaction_id'],
//        $transaction['email'],
//        $transaction['total'],
//        $transaction['merchant_ref'],
//        $transaction['memo'],
//        $transaction['status'],
//        $transaction['date'],
//        $transaction['referrer'],
//        $transaction['method']
    }

    /**
     * Generate invoice return for Transaction
     *
     * @param $transactionData
     *
     */
    public function generateInvoice($transactionData)
    {

        // Query DB If Successful

        // Mail User an Invoice
        // TODO: Implement generateInvoice() method.
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
        return $this->getConfig(strtolower(self::GATEWAY),$key);
    }

    /**
     * @param $transactionData
     *
     * @return array|string
     */
    private function serializeItemsToJson($transactionData)
    {
        $items = [ ];

        foreach ($transactionData as $key => $value) {

//            if (starts_with($key, ['item_','price_','description_'])) {
//                $items[$key] = $value;
//            }

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

        $items = json_encode($items);

        return $items;
    }

}