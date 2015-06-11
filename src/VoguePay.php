<?php


namespace LaraPayNG;

use GuzzleHttp\Client;

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
     * @throws \Dammyammy\LaraPayNG\Exceptions\UnknownPaymentGatewayException
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
    public function processTransaction($transactionData)
    {
        $client = new Client(['base_url' => 'https://ibank.gtbank.com']);

        $response = $client->get('/GTPayService/gettransactionstatus.json', [
            'query'     =>  [
                                'mertid'  => $this->app['config']['services.payment.gtpay.mert_id'],
                                'amount'  => $transactionData['amount'],
                                'tranxid' => $transactionData['id'],
                                'hash'    => $this->helper->generateVerificationHash($transactionData['id'])

                            ],
            'headers'   =>  ['Accept' => 'application/json' ]
        ]);

        dd($response->json());

//        #It is assumed that you have put the URL to this file in the notification url (notify_url)
//##of the form you submitted to voguepay.
//##VoguePay Submits transaction id to this file as $_POST['transaction_id']
//        /*--------------Begin Processing-----------------*/
//##Check if transaction ID has been submitted
//
//        if(isset($_POST['transaction_id'])){
//            //get the full transaction details as an json from voguepay
//            $json = file_get_contents('https://voguepay.com/?v_transaction_id='.$_POST['transaction_id'].'&type=json');
//            //create new array to store our transaction detail
//            $transaction = json_decode($json, true);
//
//            /*
//            Now we have the following keys in our $transaction array
//            $transaction['merchant_id'],
//            $transaction['transaction_id'],
//            $transaction['email'],
//            $transaction['total'],
//            $transaction['merchant_ref'],
//            $transaction['memo'],
//            $transaction['status'],
//            $transaction['date'],
//            $transaction['referrer'],
//            $transaction['method']
//            */
//
//            if($transaction['total'] == 0)die('Invalid total');
//            if($transaction['status'] != 'Approved')die('Failed transaction');
//
//            /*You can do anything you want now with the transaction details or the merchant reference.
//            You should query your database with the merchant reference and fetch the records you saved for this transaction.
//            Then you should compare the $transaction['total'] with the total from your database.*/
//        }

        // Save Response to DB (Keep Transaction Detail)
        // Determine If the Transaction Failed Or Succeeded & Redirect As Appropriate
            // If Success, Notify User Via Email Of their Order
            // Notify Admin Of New Order



        //        . $transactionData['verificatioHash']



//        {"Amount":"2600","MerchantReference":"FBN|WEB|UKV|19-12-2013|037312","MertID":"17","ResponseCode":"00","ResponseDescription":"Approved by Financial Institution"}
    }

    /**
     * Log Transaction
     *
     * @param $transactionData
     *
     * @return
     */
    public function logTransaction($transactionData)
    {
        // TODO: Implement logTransaction() method.
    }

    /**
     * Generate invoice return for Transaction
     *
     * @param $transactionData
     *
     * @return
     */
    public function generateInvoice($transactionData)
    {
        // TODO: Implement generateInvoice() method.
    }



}