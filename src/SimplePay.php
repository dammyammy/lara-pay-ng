<?php

namespace LaraPayNG;

use Carbon\Carbon;
use GuzzleHttp\Client;
use LaraPayNG\Contracts\PaymentGateway;
use LaraPayNG\Traits\CanGenerateInvoice;

class SimplePay extends Helpers implements PaymentGateway
{
    //    use CanGenerateInvoice;

    /**
     * Define Gateway name.
     */
    const GATEWAY = 'simplepay';

    /**
     * @param $key
     *
     * Retrieve A Config Key From VoguePay Gateway Array
     *
     * @return mixed
     */
    public function config($key = '*')
    {
        return $this->getConfig(self::GATEWAY, $key);
    }

    /**
     * @param string $transactionId
     * @param array  $transactionData
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
    public function payButton($transactionId, $transactionData = [], $class = '', $buttonTitle = 'Pay Now', $gateway = self::GATEWAY)
    {
        return $this->generateSubmitButton($transactionId, $transactionData, $class, $buttonTitle, $gateway);
    }

    public function button($productId, $transactionData = [], $class = '', $buttonTitle = 'Pay Now')
    {
        return $this->payButton($productId, $transactionData, $class, $buttonTitle, self::GATEWAY);
    }

    /**
     * Log Transaction Before Paying So as To Persist Data.
     *
     * @param $transactionData
     *
     * @return string
     */
    public function logTransaction($transactionData)
    {
        $items = $this->serializeItemsToJson($transactionData);

        $total = $this->sumItemPrices($transactionData);

        $payerId = isset($transactionData['payer_id']) ? $transactionData['payer_id'] : auth()->user()->getAuthIdentifier();

        $valueToInsert = [
            'escrow'            => isset($transactionData['escrow']) ? $transactionData['escrow'] : false,
            'freeclient'        => isset($transactionData['freeclient']) ? $transactionData['freeclient'] : true,
            'nocards'           => isset($transactionData['nocards']) ? $transactionData['nocards'] : false,
            'giftcards'         => isset($transactionData['giftcards']) ? $transactionData['giftcards'] : false,
            'chargeforcards'    => isset($transactionData['chargeforcards']) ? $transactionData['chargeforcards'] : true,
            'price'             => isset($transactionData['price']) ? $transactionData['price'] : $total,
            'setup'             => isset($transactionData['setup']) ? $transactionData['setup'] : 0.00,
            'tax'               => isset($transactionData['tax']) ? $transactionData['tax'] : 0.00,
            'shipping'          => isset($transactionData['shipping']) ? $transactionData['shipping'] : 0.00,
            'commission_amount' => isset($transactionData['commission_amount']) ? $transactionData['commission_amount'] : 0.00,
            'items'             => $items,
            'comments'          => isset($transactionData['comments']) ? $transactionData['comments'] : null,
            'action'            => isset($transactionData['action']) ? $transactionData['action'] : 'product',
            'trialperiod'       => isset($transactionData['trialperiod']) ? $transactionData['trialperiod'] : null,
            'period'            => isset($transactionData['period']) ? $transactionData['period'] : null,
            'payer_id'          => is_null($payerId) ? $payerId : null,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ];

        $table = $this->config('table');

        $transactionId = $this->dataRepository->saveTransactionDataTo($table, $valueToInsert);

        $customid = isset($transactionData['customid']) ? $transactionData['customid'] : $this->generateTransactionId($transactionId);

        $this->dataRepository->updateTransactionDataFrom($table, ['customid'  => $customid], $transactionId);

        return $customid;
    }

    /**
     * @param $transactionData
     * @param $mertId
     *
     * @return mixed
     */
    public function receiveTransactionResponse($transactionData, $mertId)
    {
        $queryString = [
            'transaction_id'                  => $transactionData['transaction_id'],
            'customid'                        => $transactionData['customid'],
            'buyer'                           => $transactionData['buyer'],
            'total'                           => floatval($transactionData['total']),
            'comments'                        => $transactionData['comments'],
            'SP_TRANSACTION_ERROR'            => $transactionData['SP_TRANSACTION_ERROR'],
            'SP_TRANSACTION_ERROR_CODE'       => $transactionData['SP_TRANSACTION_ERROR_CODE'],
            'action'                          => $transactionData['action'],
            'referer'                         => $transactionData['referer'],
            'pname'                           => $transactionData['pname'],
            'pid'                             => $transactionData['pid'],
            'quantity'                        => $transactionData['quantity'],
            'fees'                            => floatval($transactionData['fees']),
            'commission_amount'               => floatval($transactionData['comission_amount']),
            'cmd'                             => '_notify-validate',
        ];

        $client = new Client();

        $request = $client->get($this->config('gatewayUrl').'processverify.php', [
            'query'     => $queryString,
            'headers'   => ['Accept' => 'application/json'],
        ]);

        $response = $request->getBody();

        $transaction = json_decode($response, true);

        $transaction['customid'] = ($transaction['customid'] != '') ? $transaction['customid'] : $mertId;

        $result = $this->logResponse($transaction);

        return $this->collateResponse($result);
    }

    public function getTransactionDetails($transactionId)
    {
        $client = new Client();

        $queryString = [
            'user'   => $this->config('member'),
            'tranid' => $transactionId,
        ];
        $request = $client->get($this->config('gatewayUrl').'transactiondetails.php', [
            'query'     => $queryString,
            'headers'   => ['Accept' => 'application/json'],
        ]);

        $response = $request->getBody();

        return json_decode($response, true);
    }

    /**
     * Log Transaction Response.
     *
     * @param $transactionData
     *
     * @return mixed|static
     */
    public function logResponse($transactionData)
    {
        $valueToUpdate = [
            's_transaction_id'  => $transactionData['transaction_id'],
            's_customid'        => $transactionData['customid'],
            's_buyer'           => $transactionData['buyer'],
            's_total'           => floatval($transactionData['total']),
            'comments'          => $transactionData['comments'],
            'status'            => $transactionData['SP_TRANSACTION_ERROR'],
            'status_code'       => $transactionData['SP_TRANSACTION_ERROR_CODE'],
            'action'            => $transactionData['action'],
            'referrer'          => $transactionData['referer'],
            's_pname'           => $transactionData['pname'],
            's_pid'             => $transactionData['pid'],
            's_quantity'        => $transactionData['quantity'],
            's_fees'            => floatval($transactionData['fees']),
            'commission_amount' => floatval($transactionData['comission_amount']),
        ];

        $table = $this->config('table');

        $this->dataRepository->updateTransactionDataWhere('customid', $transactionData['customid'], $table, $valueToUpdate);

        return $this->dataRepository->getTransactionDataWhere('customid', $transactionData['s_customid'], $table);
    }

    /**
     * @param $transactionData
     *
     * @return array|string
     */
    public function serializeItemsToJson($transactionData)
    {
        $items = [];

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
                    'item'        => $transactionData['product'],
                    'price'       => $transactionData['price'],
                    'description' => isset($transactionData['comments'])
                        ? $transactionData['comments']
                        : 'Billed Every '.$transactionData['period'].' days',
                ],
            ]);

            return $items;
        }

        $items = json_encode($items);

        return $items;
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
