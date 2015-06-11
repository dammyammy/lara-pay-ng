<?php


namespace Dammyammy\LaraPayNG;

use Dammyammy\LaraPayNG\Exceptions\UnknownPaymentGatewayException;
//use Dammyammy\LaraPayNG\PaymentGatewayManager;
//use \Illuminate\Routing\Controllers\Controller as Controller;
use Config;
use View;
use Input;

class PaymentController extends \BaseController {

    /**
     * @var PaymentGateway
     */
    private $paymentGateway;

    /**
     * @param PaymentGatewayManager $paymentGateway
     */
    function __construct(PaymentGatewayManager $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }


    public function processPayment()
    {
        $gateway = Config::get('lara-pay-ng::gateways.driver');

        switch ($gateway)
        {
            case 'gtpay':
                return $this->processGTPayTransaction();
                break;

            case 'webpay':
                return $this->processWebPayTransaction();
                break;

            case 'voguepay':
                return $this->processVoguePayTransaction();
                break;

            default:
                throw new UnknownPaymentGatewayException;

        }

    }

    public function success($result = [])
    {
        return View::make(Config::get('lara-pay-ng::gateways.routes.success_view_name'), compact('result'));
    }

    public function failed($result = [])
    {
        return View::make(Config::get('lara-pay-ng::gateways.routes.failure_view_name'), compact('result'));
    }

    /**
     * @return mixed
     */
    private function processVoguePayTransaction()
    {
        $transactionId = Input::get('transaction_id');

        $result = $this->paymentGateway->processTransaction($transactionId);

        // Compare DB Amount & Returned amount
        // Update DB
        // Redirect as appropriate

        if ( $result->status == 'Approved' ) return $this->success($result);
        if ( $result->status == 'Failed' ) return $this->failed($result);
    }

    /**
     * @return mixed
     */
    private function processGTPayTransaction()
    {
        $transactionId = Input::get('transaction_id');

        $result = $this->paymentGateway->processTransaction($transactionId);

        // Compare DB Amount & Returned amount
        // Update DB
        // Redirect as appropriate

        if ( $result->status == 'Approved' ) return $this->success($result);
        if ( $result->status == 'Failed' ) return $this->failed($result);
    }

    /**
     * @return mixed
     */
    private function processWebPayTransaction()
    {
        $transactionId = Input::get('transaction_id');

        $result = $this->paymentGateway->processTransaction($transactionId);

        // Compare DB Amount & Returned amount
        // Update DB
        // Redirect as appropriate

        if ( $result->status == 'Approved' ) return $this->success($result);
        if ( $result->status == 'Failed' ) return $this->failed($result);
    }

}

