<?php


namespace LaraPayNG;

use Carbon\Carbon;
use GuzzleHttp\Client;
use LaraPayNG\Contracts\PaymentGateway;
use LaraPayNG\Traits\CanGenerateInvoice;

class CashEnvoy extends Helpers implements PaymentGateway
{
//    use CanGenerateInvoice;

    /**
     * Define Gateway name
     */
    const GATEWAY = 'cashenvoy';


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
    public function payButton($productId, $transactionData = [], $class = '', $buttonTitle = '', $gateway = self::GATEWAY)
    {
        return $this->generateSubmitButton($productId, $transactionData, $class, $buttonTitle, $gateway);
    }

    public function button($productId, $transactionData = [], $class = '', $buttonTitle = '')
    {
        return $this->payButton($productId, $transactionData, $class, $buttonTitle, self::GATEWAY);
    }

    /**
     * Log Transaction Before Paying So as To Persist Data
     *
     * @param $transactionData
     *
     * @return string
     */
    public function logTransaction($transactionData)
    {
        $items = $this->serializeItemsToJson($transactionData);

        $total = $this->sumItemPrices($transactionData);

        $valueToInsert = [

            'ce_amount'     => isset($transactionData['ce_amount']) ? floatval($transactionData['ce_amount']) : $total,
            'ce_customerid' => isset($transactionData['ce_customerid']) ? $transactionData['ce_customerid'] : auth()->user()->getAuthIdentifier(),
            'ce_type'       => isset($transactionData['ce_type']) ? $transactionData['ce_type'] : 'Standard',
            'ce_memo'       => isset($transactionData['ce_memo']) ? $transactionData['ce_memo'] : null,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
            'items'             => $items,
        ];

        $table = $this->config('table');

        $id = $this->dataRepository->saveTransactionDataTo($table, $valueToInsert);

        $transactionRef = isset($transactionData['ce_transref']) ? $transactionData['ce_transref'] : $this->generateTransactionReference($id);

        $valueToUpdate = ['ce_transref'  => $transactionRef];

        $this->dataRepository->updateTransactionDataFrom($table, $valueToUpdate, $id);

        return $transactionRef;
    }

    /**
     *
     * @param $transactionData
     * @param $mertId
     *
     * @return mixed
     */
    public function receiveTransactionResponse($transactionData, $mertId)
    {
        $mertId = $this->config('ce_merchantid');

        $signature = trim($this->generateVerificationHash($transactionData['ce_transref'], $gateway = 'cashenvoy'));

        $queryString = [
            'mertid'    => $mertId,
            'respformat'    => 'json',
            'transref'   => $transactionData['ce_transref'],
            'signature'      => $signature
        ];

        $client = new Client();

        $request = $client->post($this->config('gatewayUrl') . '?cmd=requery', [
            'body'     => $queryString,
            'headers'   =>  ['Accept' => 'application/json']
        ]);

        $response = $request->getBody();

        $transaction = json_decode($response, true);

        $result = $this->logResponse($transaction + $transactionData);

        return $this->collateResponse($result);
    }

    /**
     * Log Transaction Response
     *
     * @param $transactionData
     *
     * @return mixed|static
     */
    public function logResponse($transactionData)
    {
        $statusCode = $transactionData["TransactionStatus"];

        $amountCorrect = $transactionData["TransactionAmount"] == $transactionData["ce_amount"];

        $valueToUpdate = [
            "transaction_id"        => isset($transactionData["TransactionId"]) ? $transactionData["TransactionId"] : null,
            "amount"                => (($statusCode == 'C00')) ? floatval($transactionData["TransactionAmount"]) : 0.00,
            "status"                => ($amountCorrect) ? $transactionData["ce_response"] : 'Amount does not Match! Contact CashEnvoy!',
            "response_code"         => ($amountCorrect) ? $statusCode : 'C05',
            "response_description"  =>  ($amountCorrect) ? $this->responseDesctiption($statusCode) : $this->responseDesctiption('C05'),
        ];

        $table = $this->config('table');

        $this->dataRepository->updateTransactionDataWhere('ce_transref', $transactionData['ce_transref'], $table, $valueToUpdate);

        return $this->dataRepository->getTransactionDataWhere('ce_transref', $transactionData['ce_transref'], $table);
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
                    'item' => $transactionData['ce_memo'],
                    'price' => $transactionData['ce_amount'],
                    'description' => isset($transactionData['ce_memo'])
                        ? $transactionData['ce_memo']
                        : 'N/A'
                ]
            ]);

            return $items;
        }

        $items = json_encode($items);

        return $items;
    }

    private function responseDesctiption($status)
    {
        $codes = [
            'C00' => 'CashEnvoy transaction successful.',
            'C01' => 'User cancellation.',
            'C02' => 'User cancellation by inactivity.',
            'C03' => 'No transaction record.',
            'C04' => 'Insufficient funds.',
            'C05' => 'Transaction failed. Contact support@cashenvoy.com for more information.'
        ];

        return $codes[$status];
    }


    /**
     *
     * Get All Transactions
     *
     * @return mixed
     */
    public function viewAllTransactions()
    {
        return $this->getAllTransactions(self::GATEWAY);
    }

    /**
     *
     * Get All Failed Transactions
     *
     * @return mixed
     */
    public function viewFailedTransactions()
    {
        return $this->getFailedTransactions(self::GATEWAY);
    }

    /**
     *
     * Get All Successful Transactions
     *
     * @return mixed
     */
    public function viewSuccessfulTransactions()
    {
        return $this->getSuccessfulTransactions(self::GATEWAY);
    }


}
