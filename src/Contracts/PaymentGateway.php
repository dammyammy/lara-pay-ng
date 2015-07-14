<?php

namespace LaraPayNG\Contracts;

interface PaymentGateway
{
    /**
     * Log Transaction Before Paying So as To Persist Data
     *
     * @param $transactionData
     *
     * @return
     */
    public function logTransaction($transactionData);


    /**
     * @param string $productId
     * @param array $transactionData
     * @param string $class
     * @param $buttonTitle
     * @param string $gateway
     *
     * Render Pay Button For Particular Transaction To Send Buyer To Gateway Portal
     *
     * @return mixed
     */
    public function payButton($productId, $transactionData, $class, $buttonTitle, $gateway);

    /**
     *
     * @param $transactionData
     * @param $mertId
     *
     * @return mixed
     */
    public function receiveTransactionResponse($transactionData, $mertId);


    /**
     * Log Transaction Response
     *
     * @param $transactionData
     *
     * @return
     */
    public function logResponse($transactionData);


    /**
     * @param $key
     *
     * Retrieve A Config Key From Default Gateway Array
     *
     * @return mixed
     */
    public function config($key);


    /**
     *
     * Get All Transactions
     *
     * @return mixed
     */
    public function viewAllTransactions();

    /**
     *
     * Get All Failed Transactions
     *
     * @return mixed
     */
    public function viewFailedTransactions();

    /**
     *
     * Get All Successful Transactions
     *
     * @return mixed
     */
    public function viewSuccessfulTransactions();




//    /**
//     * Generate invoice return for Transaction
//     *
//     * @param $transactionData
//     *
//     * @return
//     */
//    public function generateInvoice($transactionData);

//    /**
//     * Enable sandbox API
//     *
//     * @param string $val
//     */
//    public function setSandboxMode($val);

//    /**
//     * Set merchant account.
//     *
//     * @param string $val
//     */
//    public function setMerchantAccount($val);

//    /**
//     * Transform payment fields and build to array
//     *
//     * @param array $extends
//     */
//    public function build($extends = array());
//    /**
//     * Render the HTML payment Form
//     *
//     * @param array $opts
//     */
//    public function render($opts = array());

//    /**
//     * Get post frontend result from API gateway
//     */
//    public function getFrontendResult();
//    /**
//     * Get post backend result from API gateway
//     */
//    public function getBackendResult();
}
