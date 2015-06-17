<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Routing\Controller;
use Illuminate\Session\Store;
use LaraPayNG\Exceptions\UnknownPaymentGatewayException;
use LaraPayNG\Exceptions\UnspecifiedTransactionAmountException;
use LaraPayNG\Managers\PaymentGatewayManager;
use LaraPayNG\Traits\LaraPayNGTestData;

class PaymentController extends Controller
{
    // These Contains All Test Data Used For the Tests
    use LaraPayNGTestData;
//    use NotificationResponseDeterminer;

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
    public function __construct(PaymentGatewayManager $paymentGateway, Store $session)
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
    public function checkout(Request $request)
    {

        /*********************************************************************
         *   Do Whatever You normally Would To get your Products Information
         *   An example could be to get it out of the Session Store, Via Your Cart Package or Something
         *   Or If you are Passing It Via a Request Object, Type-Hint the Method With your
         *   Request Object to get it in here.
         */
        try {

            // Let the array contain all Necessary Data Needed (For the Default Gateway)
            // i.e all Inputs for the PayButton
            // to log The Transaction (Saving To DB)

            if (config('lara-pay-ng.gateways.driver') == 'voguepay') {
                $transactionData = $this->voguePayTestData($request);
            }

            if (config('lara-pay-ng.gateways.driver') == 'gtpay') {
                $transactionData = $this->gtPayTestData($request);
            }

            if (config('lara-pay-ng.gateways.driver') == 'simplepay') {
                $transactionData = $this->simplePayTestData($request);
            }

            if (config('lara-pay-ng.gateways.driver') == 'cashenvoy') {
                $transactionData = $this->cashEnvoyTestData($request);
            }

            if (config('lara-pay-ng.gateways.driver') == 'webpay') {
                $transactionData = $this->webPayTestData($request);
            }

            $merchantRef = $this->paymentGateway->logTransaction($transactionData);

            $items = json_decode($this->paymentGateway->serializeItemsToJson($transactionData), true);

            return view('payment.confirm', compact('transactionData', 'merchantRef', 'items'));

        } catch (UnspecifiedTransactionAmountException $e){
            // Handle This Exception However you please
            // Shouldn't Ever Occur If you Do the Implementation Correctly
        } catch (UnknownPaymentGatewayException $e){
            // Handle This Exception However you please
            // Shouldn't Ever Occur If you Choose One of the Supported Gateways and Spell It right
        }
    }


    public function notification($mert_id, Request $request)
    {

        // For Situations Where a Not you prefer a Notification Url Other than Your Success or Fail Url Directly
        $result = $this->handleTransactionResponse($mert_id, $request);

        return $this->determineViewToPresent($result);
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
         * Events. To Use The Events, Uncomment the Call, create the method and write your implementation
         *********************************/

        // $this->handleNextStepsUsingEvents($result);

        return $result;
    }

    /**
     * @param $result
     *
     * @return \Illuminate\View\View
     */
    private function determineViewToPresent($result)
    {
        switch ($result['status']){
            case 'Approved':
            case 'Approved by Financial Institution':
                return view(config('lara-pay-ng.gateways.routes.success_view_name'), compact('result'));
                break;

            default:
                return view(config('lara-pay-ng.gateways.routes.failure_view_name'), compact('result'));
                break;
        }
    }

}
