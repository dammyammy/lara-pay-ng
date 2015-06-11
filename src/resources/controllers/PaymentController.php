<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaraPayNG\Exceptions\UnknownPaymentGatewayException;


class PaymentController extends Controller {
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


    public function processPayment(Request $request)
    {

        $gateway = config('lara-pay-ng.gateways.driver');

        switch ($gateway)
        {
            case 'gtpay':
                return $this->processGTPayTransaction($request);
                break;

            case 'webpay':
                return $this->processWebPayTransaction($request);
                break;

            case 'voguepay':
                return $this->processVoguePayTransaction($request);
                break;

            default:
                throw new UnknownPaymentGatewayException;
        }
    }

    public function success($result = [])
    {
        return view(config('lara-pay-ng.gateways.routes.success_view_name'), compact('result'));
    }

    public function failed($result = [])
    {
        return View.make(config('lara-pay-ng.gateways.routes.failure_view_name'), compact('result'));
    }

    /**
     * @return mixed
     */
    private function processVoguePayTransaction(Request $request)
    {
        $data = $request->all();

        $transactionId = $request->input('transaction_id');

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
    private function processGTPayTransaction(Request $request)
    {
        // GTPay Specific Processing

        $data = $request->all();

        $transactionId = $request->input('transaction_id');

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
    private function processWebPayTransaction(Request $request)
    {
        // WebPay Specific Processing
        $data = $request->all();

        $transactionId = $request->input('transaction_id');

        $result = $this->paymentGateway->processTransaction($transactionId);
        // Compare DB Amount & Returned amount
        // Update DB
        // Redirect as appropriate
        if ( $result->status == 'Approved' ) return $this->success($result);
        if ( $result->status == 'Failed' ) return $this->failed($result);
    }
}