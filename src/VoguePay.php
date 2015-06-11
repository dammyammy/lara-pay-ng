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
     * @param string $productId
     * @param array $transactionData
     * @param string $class
     * @param string $buttonTitle
     * @param string $gateway
     *
     * Render Buy Button For Particular Product
     *
     * @throws \LaraPayNG\Exceptions\UnknownPaymentGatewayException
     * @return string
     */
    public function buyButton($productId, $transactionData = [], $class = '', $buttonTitle = 'Pay Now', $gateway = self::GATEWAY)
    {
        return $this->generateSubmitButton($productId, $transactionData, $class, $buttonTitle, $gateway );
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

    /**
     * Log Transaction
     *
     * @param $transactionData
     *
     */
    public function logTransaction($transactionData)
    {

        //        memo (optional)	Provided by merchant	The transaction summary that will show on your transaction history page when you login to VoguePay
//item_x	Name of product	The name of the product being purchased. x is a value starting from 1. If there are more than 1 products, you can have item_1, item_2, item_3... as shown in the Sample HTML Form. Each item_x has a corresponding description_x and price_x
//description_x	Short description of product	The short description of the product being purchased. x corresponds to the number in item_x.
//    price_x	Price of product.	The price of the product being purchased. x corresponds to the number in item_x.
//    developer_code	A code unique to every developer. Using this code earns the developer a commission on every successful transaction made through any selected integration methods.	This optional field serves as a check for the form. Can be ommited. If included, will be used instead of the sum of all the prices.
//    store_id	A unique store identifier which identifies a particular store a transaction was made.
//    total	Total of all the prices (price_1 + price_2 + price_3...)	This optional field serves as a check for the form. Can be ommited. If included, will be used instead of the sum of all the prices.
//    recurrent

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

        $items = json_encode([

        ]);

        DB::table('voguepay_transactions')->insert([
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



}