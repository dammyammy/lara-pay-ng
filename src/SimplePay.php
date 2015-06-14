<?php


namespace LaraPayNG;

use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use LaraPayNG\Contracts\PaymentGateway;
use LaraPayNG\Traits\CanGenerateInvoice;

class SimplePay extends Helpers implements PaymentGateway
{
    use CanGenerateInvoice;

    /**
     * Define Gateway name
     */
    const GATEWAY = 'SimplePay';

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
        $valueToUpdate = [
            "transaction_id"  => $transactionData["transaction_id"],
            "s_customid"           => $transactionData["customid"],
            "s_buyer"           => $transactionData["buyer"],
            "s_total"           => floatval($transactionData["total"]),
            "comments"              => $transactionData["comments"],
            "status"            => $transactionData["SP_TRANSACTION_ERROR"],
            "status_code"            => $transactionData["SP_TRANSACTION_ERROR_CODE"],
            "s_action"      => $transactionData["action"],
            "referrer"          => $transactionData["referer"],
            "s_pname"  => floatval($transactionData["pname"]),
            "s_pid"  => floatval($transactionData["pid"]),
            "s_quantity"  => floatval($transactionData["quantity"]),
            "s_fees"=> floatval($transactionData["fees"]),
            "s_commission_amount"   => $transactionData["comission_amount"],
        ];



        DB::table(config('lara-pay-ng.gateways.voguepay.table'))
            ->where('customid', $transactionData['customid'])
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
}
