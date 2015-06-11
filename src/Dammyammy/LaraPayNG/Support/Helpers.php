<?php


namespace Dammyammy\LaraPayNG\Support;

use Dammyammy\LaraPayNG\Exceptions\UnknownConfigException;
use Dammyammy\LaraPayNG\Exceptions\UnspecifiedPayItemIdException;
use Dammyammy\LaraPayNG\Exceptions\UnspecifiedTransactionAmountException;
use Dammyammy\LaraPayNG\Exceptions\UnknownPaymentGatewayException;
use Illuminate\Config\Repository;


class Helpers {

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $productId
     * @param array $transactionData
     * @param string $class
     * @param string $buttonTitle
     *
     * Render Buy Button For Particular Product
     *
     * @return string
     * @throws UnknownPaymentGatewayException
     */
    public function buyButton($productId, $transactionData = [], $class = '', $buttonTitle = 'Pay Now')
    {
        $gateway = $this->getConfig('driver');

        return $this->generateSubmitButton($productId, $transactionData, $class, $buttonTitle, $gateway );
    }

    /**
     * @param float $amount
     *
     * @return string
     */
    public function inNaira($amount)
    {
        return '&#8358; ' . number_format($amount, 2);
    }

    /**
     * Building HTML Form
     *
     * Gateways use this method to build hidden form for product Buy Button.
     *
     * @param string $productId
     * @param array $transactionData
     * @param string $class
     * @param string $buttonTitle
     * @param string $gateway
     *
     * @throws UnknownPaymentGatewayException
     *
     * @return string HTML
     */
    protected  function generateSubmitButton($productId, $transactionData, $class, $buttonTitle, $gateway)
    {

        switch (strtolower($gateway))
        {
            case 'gtpay':
                return $this->generateSubmitButtonForGTPay($productId, $transactionData, $class, $buttonTitle);

                break;

            case 'webpay':
                return $this->generateSubmitButtonForWebPay($productId, $transactionData, $class, $buttonTitle);

                break;

            case 'voguepay':
                return $this->generateSubmitButtonForVoguePay($productId, $transactionData, $class, $buttonTitle);

                break;

            default:
                throw new UnknownPaymentGatewayException;

                break;
        }
    }



    /**
     * Generate Transaction Id For Product
     * @param $productId
     *
     * @return string
     */
    public function generateTransactionId($productId)
    {
        return $this->getConfig('transactionIdPrefix') . $productId;
    }

    /**
     * @param $productId
     * @param $transactionAmount
     * @param null $payItemId
     * @param string $gateway
     *
     * GTPay:  gtpay_tranx_id + gtpay_tranx_amt + gtpay_tranx_noti_url + hashkey
     * WebPay: tnx_ref + product_id + pay_item_id + amount + site_redirect_url + <the provided MAC key>
     *
     * @throws UnknownPaymentGatewayException
     * @return string
     */
    public function generateTransactionHash($productId, $transactionAmount, $gateway = 'gtpay', $payItemId = null)
    {
        switch ($gateway)
        {
            case 'gtpay':
                return hash(
                            'sha512',
                            $this->generateTransactionId($productId) . $transactionAmount .
                            $this->getConfig('gtpay', 'gtpay_tranx_noti_url') .
                            $this->getConfig('gtpay', 'hashkey'),
                            false
                        );

                break;

            case 'webpay':
                return hash(
                            'sha512',
                            $this->generateTransactionId($productId) . $productId .
                            $payItemId . $transactionAmount .
                            $this->getConfig('webpay', 'site_redirect_url') .
                            $this->getConfig('webpay', 'hashkey'),
                            false
                        );

                break;

            case 'voguepay':
                return true;
                break;

            default:
                throw new UnknownPaymentGatewayException;
                break;
        }

    }



    public function generateVerificationHash($tranx_id, $gateway = 'gtpay', $product_id = '4220')
    {
        if ($gateway == 'webpay')
        {
//     productid, transactionreference and your hash key
//       $product_id = substr($tranx_id, strpos($tranx_id, 'D') + 1);

            return hash('sha512', $product_id . $tranx_id . $this->getConfig('webpay', 'hashkey'), false);
        }

//    mertid + tranxid + hashkey
        return hash('sha512', $this->getConfig('gtpay', 'gtpay_mert_id') . $tranx_id . $this->getConfig('gtpay', 'hashkey'), false);
    }

    /**
     * @param $driver
     *
     * @throws UnknownPaymentGatewayException
     * @return string
     */
    protected function determineGatewayUrl($driver)
    {

        switch ( $driver )
        {
            case 'gtpay':
                $gatewayUrl = $this->getConfig('gtpay', 'gatewayUrl');
                break;

            case 'webpay':
                $gatewayUrl = $this->getConfig('webpay', 'gatewayUrl');
                break;

            case 'voguepay':
                $gatewayUrl = $this->getConfig('voguepay', 'gatewayUrl');
                break;

            case 'default':
                throw new UnknownPaymentGatewayException;
                break;
        }

        return $gatewayUrl;
    }

    /**
     * @param $key
     *
     * Retrieve A Config Key From Default Gateway Array
     *
     * @return mixed
     */
    public function config($key)
    {
        return $this->getConfig($this->config->get('lara-pay-ng::gateways.driver'), $key);
    }

    /**
     * @param string $gateway
     * @param null|string $key
     *
     * @throws UnknownConfigException
     * @return array|mixed|string
     */
    protected function getConfig($gateway = '', $key = '*')
    {
        $keywithdot = '.' . $key;

        switch ( $gateway )
        {
            case 'driver':
                return $this->config->get('lara-pay-ng::gateways.driver');
                break;

            case 'transactionIdPrefix':
                return $this->config->get('lara-pay-ng::gateways.transactionIdPrefix');
                break;

            case 'webpay':
                return $this->getGatewayConfig($gateway, $key, $keywithdot);
                break;

            case 'voguepay':
                return $this->getGatewayConfig($gateway, $key, $keywithdot);
                break;

            case 'gtpay':
                return $this->getGatewayConfig($gateway, $key, $keywithdot);
                break;

            default:
                throw new UnknownConfigException('Unknown Config Variable Requested!!');
                break;
        }

    }

    /**
     * @param $productId
     * @param $transactionData
     * @param $class
     * @param $buttonTitle
     *
     * @throws UnspecifiedTransactionAmountException
     * @throws UnknownPaymentGatewayException
     * @return string
     */
    private function generateSubmitButtonForGTPay($productId, $transactionData, $class, $buttonTitle)
    {
        $formId = 'PayViaGTPay';

        $gatewayUrl = $this->getConfig('gtpay', 'gatewayUrl');

        $hiddens = [ ];
        $addition = [ ];
        foreach ( $transactionData as $key => $val )
        {
            $hiddens[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
        }

        foreach ( $this->getConfig('gtpay') as $key => $val )
        {
            if(!is_null($this->getConfig('gtpay', $key)) AND $key != 'gatewayUrl' AND $key != 'hashkey'
                AND $key != 'success_url' AND $key != 'fail_url' AND array_key_exists($key, $transactionData) === false
            )
            {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }
        }

        $transactionId[] = '<input type="hidden" name="gtpay_tranx_id" value="' . $this->generateTransactionId($productId) . '" />' . "\n";

        if (! isset($transactionData['gtpay_tranx_amt'])) throw new UnspecifiedTransactionAmountException;

        $hash = '<input type="hidden" name="gtpay_tranx_hash" value="' . $this->generateTransactionHash($productId, $transactionData['gtpay_tranx_amt'], $gateway = 'gtpay') . '" />' . "\n";


        $addition[] = '<button type="submit"  class="' . $class . '">' . $buttonTitle . '</button>';

        $form = '
            <form method="POST" action="' . $gatewayUrl . '" id="' . $formId . '">
                ' . implode('', $transactionId) . '
                ' . implode('', $configs) . '
                ' . implode('', $hiddens) . '
                ' . $hash . '
                ' . implode('', $addition) . '
            </form>
        ';

        return $form;
    }

    /**
     * @param $productId
     * @param $transactionData
     * @param $class
     * @param $buttonTitle
     *
     * @throws UnknownPaymentGatewayException
     * @throws UnspecifiedPayItemIdException
     * @throws UnspecifiedTransactionAmountException
     * @return string
     */
    private function generateSubmitButtonForWebPay($productId, $transactionData, $class, $buttonTitle)
    {

        $formId = 'PayViaWebPay';

        $gatewayUrl = $this->getConfig('webpay', 'gatewayUrl') . '/pay';

        $hiddens = [ ];
        $addition = [ ];
        foreach ( $transactionData as $key => $val )
        {
            $hiddens[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
        }

        foreach ( $this->getConfig('webpay') as $key => $val )
        {
            if(!is_null($this->getConfig('webpay', $key)) AND $key != 'gatewayUrl' AND $key != 'hashkey'
                AND array_key_exists($key, $transactionData) === false
            )
            {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }
        }

        $transactionId[] = '<input type="hidden" name="txn_ref" value="' . $this->generateTransactionId($productId) . '" />' . "\n";
        $productId = '<input type="hidden" name="product_id" value="' . $productId . '" />' . "\n";

        if (! isset($transactionData['amount'])) throw new UnspecifiedTransactionAmountException;
        if (! isset($transactionData['pay_item_id'])) throw new UnspecifiedPayItemIdException;

        $hash = '<input type="hidden" name="hash" value="' . $this->generateTransactionHash($productId, $transactionData['amount'], 'webpay', $transactionData['pay_item_id']) . '" />' . "\n";


        $addition[] = '<button type="submit"  class="' . $class . '">' . $buttonTitle . '</button>';

        $form = '
            <form method="POST" action="' . $gatewayUrl . '" id="' . $formId . '">
                ' . implode('', $transactionId) . '
                ' . $productId . '
                ' . implode('', $configs) . '
                ' . implode('', $hiddens) . '
                ' . $hash . '
                ' . implode('', $addition) . '

            </form>
        ';

        return $form;


    }

    /**
     * @param string $productId
     * @param array $transactionData
     * @param string $class
     * @param string $buttonTitle
     *
     * @return string
     */
    private function generateSubmitButtonForVoguePay($productId, $transactionData, $class, $buttonTitle)
    {
        $voguePayButtons = [
            'buynow_blue.png', 'buynow_red.png', 'buynow_green.png', 'buynow_grey.png', 'addtocart_blue.png',
            'addtocart_red.png', 'addtocart_green.png', 'addtocart_grey.png', 'checkout_blue.png',
            'checkout_red.png', 'checkout_green.png', 'checkout_grey.png', 'donate_blue.png', 'donate_red.png',
            'donate_green.png', 'donate_grey.png', 'subscribe_blue.png', 'subscribe_red.png',
            'subscribe_green.png', 'subscribe_grey.png', 'make_payment_blue.png', 'make_payment_red.png',
            'make_payment_green.png', 'make_payment_grey.png',
        ];

        $formId = 'PayViaVoguePay';

        $gatewayUrl = $this->getConfig('voguepay', 'gatewayUrl');

        $hiddens = [ ];
        $configs = [ ];
        $addition = [ ];

        foreach ( $transactionData as $key => $val )
        {

            $hiddens[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
        }

        foreach ( $this->getConfig('voguepay') as $key => $val )
        {
            if(!is_null($this->getConfig('voguepay', $key)) AND $key != 'gatewayUrl' AND $key != 'submitButton'
                AND array_key_exists($key, $transactionData) === false
            )
            {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }
        }

        $transactionId[] = '<input type="hidden" name="merchant_ref" value="' . $this->generateTransactionId($productId) . '" />' . "\n";

        $addition[] = in_array($this->getConfig('voguepay', 'submitButton'), $voguePayButtons)
            ? '<input type="image"  src="//voguepay.com/images/buttons/' .
            $this->getConfig('voguepay', 'submitButton') . '" alt="Submit">'

            : '<input type="submit"  class="' . $class . '">' . $buttonTitle . '</input>';

        $form = '
            <form method="POST" action="' . $gatewayUrl . '" id="' . $formId . '">
                ' . implode('', $configs) . '
                ' . implode('', $transactionId) . '
                ' . implode('', $hiddens) . '
                ' . implode('', $addition) . '
            </form>
        ';

        return $form;
    }

    /**
     * @param $gateway
     * @param $key
     * @param $keywithdot
     *
     * @throws UnknownConfigException
     * @return mixed|string
     */
    private function getGatewayConfig($gateway, $key, $keywithdot)
    {
        if ( $key == '*' ) return $this->config->get('lara-pay-ng::gateways.' . $gateway);

        if ( ! array_key_exists($key, $this->config->get('lara-pay-ng::gateways.' . $gateway)) )
            throw new UnknownConfigException('Trying to get an Unknown ' . $gateway . $key . ' Config');
//        return 'Trying to get an Unknown ' . $gateway . ' Config';

        return $this->config->get('lara-pay-ng::gateways.'. $gateway . $keywithdot);
    }


    /**
     * @param $gateway
     * @param $key
     *
     * @return mixed
     */

//    public function generateTransactionData($dessert, $transactionId)
//    {
//        return 'name=' . $dessert->present()->name . ';pre=' . $dessert->present()->buyPrice
//        . ';buyer=' . currentUserName() . '; transactionId=' . $transactionId;
//    }




//    public function generateTransactionMemo($dessert)
//    {
//        return 'Name: ' . $dessert->present()->name . '; Price: ' . $dessert->present()->buyPrice
//        . '; Buyer: ' . currentUserName();
//    }


} 