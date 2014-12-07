<?php


namespace Dammyammy\LaraPayNG\Gateways\GTPay;

use Dammyammy\LaraPayNG\PaymentGateway;
use Dammyammy\LaraPayNG\Support\Helpers;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class GTPay implements PaymentGateway {


    public function __construct()
    {

        $this->helper = new Helpers();
        $this->config = new Config();
    }

    /**
     * Define Gateway name
     */
    const GATEWAY = 'GTPay';



    public function processTransaction($transactionData)
    {
        $client = new Client(['base_url' => 'https://ibank.gtbank.com']);

        $response = $client->get('/GTPayService/gettransactionstatus.json', [
            'query'     =>  [
                'mertid'  => $this->config->get('services.payment.gtpay.mert_id'),
                'amount'  => $transactionData['amount'],
                'tranxid' => $transactionData['id'],
                'hash'    => $this->helper->generateVerificationHash($transactionData['id'])

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
}