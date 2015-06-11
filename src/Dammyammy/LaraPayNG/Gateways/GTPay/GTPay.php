<?php


namespace Dammyammy\LaraPayNG\Gateways\GTPay;

use Dammyammy\LaraPayNG\PaymentGateway;
use Dammyammy\LaraPayNG\Support\Helpers;
use GuzzleHttp\Client;

class GTPay extends Helpers implements PaymentGateway {


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
                'mertid'  => $this->config('gtpay_mert_id'),
                'amount'  => $transactionData['amount'],
                'tranxid' => $transactionData['id'],
                'hash'    => $this->generateVerificationHash($transactionData['id'])

            ],
            'headers'   =>  ['Accept' => 'application/json' ]
        ]);

        dd($result = $response->json());

        // Save Response to DB (Keep Transaction Detail)
        // Determine If the Transaction Failed Or Succeeded & Redirect As Appropriate
        // If Success, Notify User Via Email Of their Order
        // Notify Admin Of New Order



        //        . $transactionData['verificatioHash']

        if(($result['Amount'] == $transactionData['Amount']) AND ($result['ResponseCode'] == '00'))
        {
            // It Succeeded
//            Ancelotti is perfect for Madrid, says Sacchi; Dortmund with no room for error in visit to Hertha Berlin; Real Madrid cannot make mistakes - Navas;
//            DB::
        }



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

}