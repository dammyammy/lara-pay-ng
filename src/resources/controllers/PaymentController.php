<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Routing\Controller;
use Illuminate\Session\Store;
use LaraPayNG\Exceptions\UnknownPaymentGatewayException;
use LaraPayNG\PaymentGatewayManager;


class PaymentController extends Controller {
    /**
     * @var PaymentGateway
     */
    private $paymentGateway;

    /**
     * @var Store
     */
    private $session;

    /**
     * @param PaymentGatewayManager $paymentGateway
     * @param Store $session
     */
    function __construct(PaymentGatewayManager $paymentGateway, Store $session)
    {
        $this->paymentGateway = $paymentGateway;
        $this->session = $session;
    }

    /**
     * @return \Illuminate\View\View
     *
     * Page before proceeding to checkout
     *
     */
    public function orders()
    {
        return view('payment.orders');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     *
     * Final Checkout Confirmation
     */
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

        $transactionData = [
            'item_1' => 'Black Aso Oke',
            'price_1' => 500.00,
            'description_1' => 'That Aso Oke Mumsi Wants',

            'item_2' => 'Red Aso Oke',
            'price_2' => 730.00,
            'description_2' => 'That Aso Oke Tosin Wants',

            'item_3' => 'Silver Aso Oke',
            'price_3' => 900.00,
            'description_3' => 'That Aso Oke I Want',
        ];

        $merchantRef = $this->paymentGateway->logTransaction($transactionData);

        $items = json_decode($this->paymentGateway->serializeItemsToJson($transactionData),true);

        return view('payment.confirm', compact('transactionData', 'merchantRef', 'items'));

    }


    public function notification($mert_id, Request $request)
    {
        $result = $this->handleTransactionResponse($mert_id, $request);

        // In case you prefer a Notification Url Other than Your Success or Fail Url
    }

    /**
     * @param $mert_id
     * @param Request $request
     *
     * @return \Illuminate\View\View
     *
     * Success Redirect Url
     */
    public function success($mert_id, Request $request)
    {
        $result = $this->handleTransactionResponse($mert_id, $request);

        return view(config('lara-pay-ng.gateways.routes.success_view_name'), compact('result'));
    }

    /**
     * @param $mert_id
     * @param Request $request
     *
     * @return \Illuminate\View\View
     *
     * Failure Redirect Url
     */
    public function failed($mert_id, Request $request)
    {
        $result = $this->handleTransactionResponse($mert_id, $request);

        return view(config('lara-pay-ng.gateways.routes.failure_view_name'), compact('result'));
    }

    /**
     * @param $mert_id
     * @param Request $request
     *
     * Handle Gateway Response
     * @return mixed
     */
    private function handleTransactionResponse($mert_id, Request $request)
    {
        $data = $request->all();

        $result = $this->paymentGateway->receiveTransactionResponse($data, $mert_id);

        /*********************************
         * $result contains all information regarding the transaction, This would be a perfect
         * place to leverage Events to Do Whatever eg. Send an Invoice, Notify admin of
         * failed transactions, confirm if total is the same so you can rest easy etc.
         * You could do your normal Procedural Approach As Well, If You are not so comfortable with
         * Events. To Use The Events, Uncomment the Method and its Call and write your implementation
         *********************************/

        // $this->handleNextStepsUsingEvents($result);

        return $result;
    }
}
