<?php


namespace LaraPayNG;

use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use LaraPayNG\Contracts\PaymentGateway;
use LaraPayNG\Traits\CanGenerateInvoice;

class CashEnvoy  extends Helpers implements PaymentGateway
{
    use CanGenerateInvoice;

    /**
     * Define Gateway name
     */
    const GATEWAY = 'CashEnvoy';

    /**
     * Log Transaction Before Paying So as To Persist Data
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
