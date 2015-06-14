<?php


namespace LaraPayNG;

use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;

class GTPay extends Helpers implements PaymentGateway
{
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
    public function payButton($productId, $transactionData = [], $class = '', $buttonTitle = 'Pay Now', $gateway = self::GATEWAY)
    {
        return $this->generateSubmitButton($productId, $transactionData, $class, $buttonTitle, $gateway);
    }


    public function sendTransactionToGateway($transactionData)
    {
        $client = new Client(['base_url' => 'https://ibank.gtbank.com']);

        $response = $client->get('/GTPayService/gettransactionstatus.json', [
            'query'     =>  [
                'mertid'  => $this->app['config']['lara-pay-ng.gateways.gtpay.mert_id'],
                'amount'  => $transactionData['amount'],
                'tranxid' => $transactionData['id'],
                'hash'    => $this->generateVerificationHash($transactionData['id'])

            ],
            'headers'   =>  ['Accept' => 'application/json' ]
        ]);

        dd($response->json());

        // Save Response to DB (Keep Transaction Detail)
        // Determine If the Transaction Failed Or Succeeded & Redirect As Appropriate
        // If Success, Notify User Via Email Of their Order
        // Notify Admin Of New Order



        //        . $transactionData['verificatioHash']



//        {"Amount":"2600","MerchantReference":"FBN|WEB|UKV|19-12-2013|037312","MertID":"17","ResponseCode":"00","ResponseDescription":"Approved by Financial Institution"}
    }

    //    public function pay()
//    {
//
//        $client = new Client(['base_url' => TRANSACTION_URL]);
//
//        $response = $client->get('/places', ['headers' => ['Accept' => 'application/x-yaml']]);
//
//        $response->getBody();
//
//    }
//
//    public function processPayment(){}
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

    /**
     *
     * @return mixed
     */
    public function receiveTransactionResponse($transactionData, $mertId)
    {
        // TODO: Implement receiveTransactionResponse() method.
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
        // TODO: Implement logResponse() method.
    }
}
