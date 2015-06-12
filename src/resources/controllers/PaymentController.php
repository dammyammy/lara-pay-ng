<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaraPayNG\Exceptions\UnknownPaymentGatewayException;
use LaraPayNG\PaymentGatewayManager;


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


    public function checkout()
    {

        /*********************************************************************
        *   Do Whatever You normally Would To get your Products Information
        *   An example could be to get it out of the Session Store, Via Your Cart Package or Something
        *   Or If you are Passing It Via a Request Object, Type-Hint the Method With your
        *   Request Object to get it in here.
        */


        // Let the array contain all Necessary Data Needed (For the Default Gateway)
        // i.e all Inputs for the PayButton
        // to log The Transaction (Saving To DB)
        $transactionData = [];


        $transactionId = $this->paymentGateway->logTransaction($transactionData);


        return redirect()->route('payment-page')
                        ->with('transactionData', $transactionData)
                        ->with('transactionId', $transactionId);
    }


    public function paymentPage()
    {
        $transactionData = Session::get( 'transactionData' );
        $transactionId = Session::get( 'transactionId' );

        return view('payment-page', compact('transactionData', 'transactionId'));
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

    public function notification(Request $request)
    {
        $data = $request->all();

        return $this->paymentGateway->receiveTransactionResponse($data);
    }

    public function success()
    {
        return view(config('lara-pay-ng.gateways.routes.success_view_name'));
    }

    public function failed()
    {
        return view(config('lara-pay-ng.gateways.routes.failure_view_name'));
    }

    /**
     * @return mixed
     */
    private function processVoguePayTransaction(Request $request)
    {
        $data = $request->all();

        $result = $this->paymentGateway->sendTransactionToGateway($data);
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

//        $transactionId = $request->input('transaction_id');

        $result = $this->paymentGateway->sendTransactionToGateway($data);
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

        $result = $this->paymentGateway->sendTransactionToGateway($data);
        // Compare DB Amount & Returned amount
        // Update DB
        // Redirect as appropriate
        if ( $result->status == 'Approved' ) return $this->success($result);
        if ( $result->status == 'Failed' ) return $this->failed($result);
    }
}
