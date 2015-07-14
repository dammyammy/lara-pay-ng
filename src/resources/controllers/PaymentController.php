<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Routing\Controller;
use Illuminate\Session\Store;
use LaraPayNG\Exceptions\UnknownPaymentGatewayException;
use LaraPayNG\Exceptions\UnspecifiedTransactionAmountException;
use LaraPayNG\Facades\GTPay;
use LaraPayNG\Facades\VoguePay;
use LaraPayNG\Facades\CashEnvoy;
use LaraPayNG\Facades\SimplePay;
use LaraPayNG\Facades\WebPay;
use LaraPayNG\Managers\PaymentGatewayManager;
use LaraPayNG\Traits\DetermineViewToPresent;
use LaraPayNG\Traits\LaraPayNGTestData;

class PaymentController extends Controller
{

    use LaraPayNGTestData; // These Contains All Test Data Used For the Tests, Remove after implementing your own
    use DetermineViewToPresent; // These Determines If a Failure Or Success View is to be Shown. Leave It

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

//        return \Pay::button(2,['total' => 300]);
        return view('vendor.lara-pay-ng.orders');
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
            // You need to create your own TransactionData and not use the testTransactionData
            // i.e all Inputs for the PayButton

//            $transactionData = $this->testTransactionData($request);
            $transactionData = $this->allInOneTestData($request);

            $merchantRef = $this->paymentGateway->logTransaction($transactionData);

            $items = json_decode($this->paymentGateway->serializeItemsToJson($transactionData), true);

            return view('vendor.lara-pay-ng.confirm', compact('transactionData', 'merchantRef', 'items'));

        } catch (UnspecifiedTransactionAmountException $e){
            dd($e);
            // Handle This Exception However you please
            // Shouldn't Ever Occur If you Do the Implementation Correctly
        } catch (UnknownPaymentGatewayException $e){

            dd($e);
            // Handle This Exception However you please
            // Shouldn't Ever Occur If you Choose One of the Supported Gateways and Spell It right
        }
    }


    public function notification($mert_id, Request $request)
    {
//        dd($request->header('origin'));

        // For Situations Where a Not you prefer a Notification Url Other than Your Success or Fail Url Directly
        $result = $this->handleTransactionResponse($mert_id, $request);

        $this->dispatchAppropriateEvents($result);

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

        $this->dispatchAppropriateEvents($result);

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

        $this->dispatchAppropriateEvents($result);

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

        $origin = $request->header('origin');

        switch($origin) {
            case str_contains($origin, 'gtbank'):
                $result = GTPay::receiveTransactionResponse($data, $mert_id);
                break;

            case str_contains($origin, 'voguepay'):
                $result = VoguePay::receiveTransactionResponse($data, $mert_id);
                break;

            case str_contains($origin, 'simplepay'):
                $result = SimplePay::receiveTransactionResponse($data, $mert_id);
                break;

            case str_contains($origin, 'cashenvoy'):
                $result = CashEnvoy::receiveTransactionResponse($data, $mert_id);
                break;

            case str_contains($origin, 'webpay'):
                $result = WebPay::receiveTransactionResponse($data, $mert_id);
                break;

            default:
                $result = $this->paymentGateway->receiveTransactionResponse($data, $mert_id);

                break;
        }


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
}
