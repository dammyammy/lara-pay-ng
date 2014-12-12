<?php


namespace Dammyammy\LaraPayNG;


interface PaymentGateway {

//    public function pay();
//
//    public function processPayment();


//    /**
//     * @param $driver
//     *
//     *
//     * @return mixed
//     */
//    public function via(PaymentGateway $driver);

//    public function forItem($transactionData);

    /**
     * @param string $productId
     * @param array $transactionData
     * @param string $class
     * @param $buttonTitle
     * @param string $gateway
     *
     * Render Buy Button For Particular Product
     *
     * @return mixed
     */
    public function buyButton($productId, $transactionData, $class, $buttonTitle, $gateway);

    /**
     * @param $transactionData
     *
     * @return mixed
     */
    public function processTransaction($transactionData);


    /**
     * Log Transaction
     *
     * @param $transactionData
     *
     * @return
     */
    public function logTransaction($transactionData);


    /**
     * Generate invoice return for Transaction
     *
     * @param $transactionData
     *
     * @return
     */
    public function generateInvoice($transactionData);


    /**
     * @param $key
     *
     * Retrieve A Config Key From Default Gateway Array
     *
     * @return mixed
     */
    public function config($key);


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