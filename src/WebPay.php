<?php

namespace LaraPayNG;

use DB;
use GuzzleHttp\Client;
use LaraPayNG\Contracts\PaymentGateway;
use LaraPayNG\Traits\CanGenerateInvoice;

class WebPay extends Helpers implements PaymentGateway
{
    //    use CanGenerateInvoice;

    /**
     * Define Gateway name.
     */
    const GATEWAY = 'webpay';

    /**
     * @param $key
     *
     * Retrieve A Config Key From WebPay Gateway Array
     *
     * @return mixed
     */
    public function config($key = '*')
    {
        return $this->getConfig(self::GATEWAY, $key);
    }

    /**
     * @param $productId
     * @param $transactionData
     * @param string $class
     * @param string $buttonTitle
     * @param string $gateway
     *
     * Render Pay Button For Particular Product
     *
     * @throws \LaraPayNG\Exceptions\UnknownPaymentGatewayException
     *
     * @return string
     */
    public function payButton($productId, $transactionData = [], $class = '', $buttonTitle = 'Pay Now', $gateway = self::GATEWAY)
    {
        return $this->generateSubmitButton($productId, $transactionData, $class, $buttonTitle, $gateway);
    }

    public function button($productId, $transactionData = [], $class = '', $buttonTitle = 'Pay Now')
    {
        return $this->generateSubmitButton($productId, $transactionData, $class, $buttonTitle, self::GATEWAY);
    }

    public function sendTransactionToGateway($transactionData)
    {

//        https://stageserv.interswitchng.com/test_paydirect/api/v1/gettransaction.json

//        product_id	The PAYDirect product
//numeric
//txn_ref	Transaction reference sent in the POST by the merchant
//alphanumeric
//amount	Original amount sent in the transaction, in small denomination
//numeric
//Hash	SHA512 hash of productid, transactionreference and your hash key
//This should be sent in the header of the request as Hash

//        'id'      => $transactionId = Input::get('txnref'),
//                'payRef'  => $transactionStatus = Input::get('payRef'),
//                'retRef'  => $transactionCurrency = Input::get('retRef'),
//                'cardNum' => $transactionStatusMsg = Input::get('cardNum'),
//                'apprAmt' => $transactionAmount = Input::get('apprAmt'),

//        dd($transactionData);

        $client = new Client(['base_url' => 'https://stageserv.interswitchng.com']);

        $response = $client->get('/test_paydirect/api/v1/gettransaction.json', [
            'query'     => [
                'product_id'  => $transactionData['productId'],
                'amount'      => $transactionData['amount'],
                'txn_ref'     => $transactionData['id'],
                'Hash'        => $this->helper->generateVerificationHash($transactionData['id'], self::GATEWAY, $transactionData['productId']),

            ],
            'headers' => ['Accept' => 'application/json'],
        ]);

        dd($response->json());

        // Save Response to DB (Keep Transaction Detail)
        // Determine If the Transaction Failed Or Succeeded & Redirect As Appropriate
        // If Success, Notify User Via Email Of their Order
        // Notify Admin Of New Order

        //        . $transactionData['verificatioHash']

//        {"Amount":"2600","MerchantReference":"FBN|WEB|UKV|19-12-2013|037312","MertID":"17","ResponseCode":"00","ResponseDescription":"Approved by Financial Institution"}
    }

    /**
     * Log Transaction.
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
     * @return mixed
     */
    public function receiveTransactionResponse($transactionData, $mertId)
    {
        // TODO: Implement receiveTransactionResponse() method.
    }

    /**
     * Log Transaction Response.
     *
     * @param $transactionData
     *
     * @return
     */
    public function logResponse($transactionData)
    {
        // TODO: Implement logResponse() method.
    }

    /**
     * Get All Transactions.
     *
     * @return mixed
     */
    public function viewAllTransactions()
    {
        return $this->getAllTransactions(self::GATEWAY);
    }

    /**
     * Get All Failed Transactions.
     *
     * @return mixed
     */
    public function viewFailedTransactions()
    {
        return $this->getFailedTransactions(self::GATEWAY);
    }

    /**
     * Get All Successful Transactions.
     *
     * @return mixed
     */
    public function viewSuccessfulTransactions()
    {
        return $this->getSuccessfulTransactions(self::GATEWAY);
    }
}
