<?php


namespace LaraPayNG;

use LaraPayNG\DataRepositories\DataRepository;
use LaraPayNG\Exceptions\UnspecifiedFieldsInTransactionData;
use LaraPayNG\Exceptions\UnspecifiedPayItemIdException;
use LaraPayNG\Exceptions\UnspecifiedTransactionAmountException;
use LaraPayNG\Exceptions\UnknownPaymentGatewayException;
use Illuminate\Contracts\Config\Repository as Config;

class Helpers
{
    /**
     * @var Repository
     */
    protected $config;
    /**
     * @var DataRepository
     */
    protected $dataRepository;

    /**
     * @param DataRepository $dataRepository
     * @param Config $config
     */
    public function __construct(DataRepository $dataRepository, Config $config)
    {
        $this->config = $config;
        $this->dataRepository = $dataRepository;
    }

    /**
     * @param string $transactionId
     * @param array $transactionData
     * @param string $class
     * @param string $buttonTitle
     *
     * Render Pay Button For Particular Product
     *
     * @return string
     * @throws UnknownPaymentGatewayException
     */
    public function payButton($transactionId, $transactionData = [], $class = '', $buttonTitle = 'Pay Now')
    {
        $gateway = strtolower($this->getConfig('driver'));

        return $this->generateSubmitButton($transactionId, $transactionData, $class, $buttonTitle, $gateway);
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
     * @param float $amount
     *
     * @return string
     */
    public function inDollars($amount)
    {
        return '$ ' . number_format($amount, 2);
    }

    /**
     * @param $transactionAmount
     *
     * @return string
     */
    protected function toFloat($transactionAmount, $decimalpoints = 2)
    {
        return number_format($transactionAmount, $decimalpoints, '.', '');
    }

    /**
     * @param $transactionAmount
     *
     * @return string
     */
    protected function toCoins($transactionAmount)
    {
        return round($transactionAmount, 2)*100;
    }

    /**
     * Building HTML Form
     *
     * Gateways use this method to build hidden form for product Buy Button.
     *
     * @param string $transactionId
     * @param array $transactionData
     * @param string $class
     * @param string $buttonTitle
     * @param string $gateway
     *
     * @throws UnknownPaymentGatewayException
     *
     * @return string HTML
     */
    protected function generateSubmitButton($transactionId, $transactionData, $class, $buttonTitle, $gateway)
    {
        switch ($gateway) {
            case 'gtpay':
                return $this->generateSubmitButtonForGTPay($transactionId, $transactionData, $class, $buttonTitle);

                break;

            case 'webpay':
                return $this->generateSubmitButtonForWebPay($transactionId, $transactionData, $class, $buttonTitle);

                break;

            case 'voguepay':
                return $this->generateSubmitButtonForVoguePay($transactionId, $transactionData, $class, $buttonTitle);

                break;

            case 'simplepay':
                return $this->generateSubmitButtonForSimplePay($transactionId, $transactionData, $class, $buttonTitle);

                break;

            case 'cashenvoy':
                return $this->generateSubmitButtonForCashEnvoy($transactionId, $transactionData, $class, $buttonTitle);

                break;



            default:
                throw new UnknownPaymentGatewayException;

                break;
        }
    }



    /**
     * Generate Transaction Id For Product
     * @param $transactionId
     *
     * @return string
     */
    public function generateTransactionId($transactionId)
    {
        $gateway = strtolower($this->getConfig('driver'));

        $tranxPrefix = $this->getConfig('transactionIdPrefix');

        if($gateway == 'cashenvoy')
        {
            if(!ctype_alnum($tranxPrefix))
            {
                $tranxPrefix = preg_replace('/[^\p{L}\p{N}\s]/u', '', $tranxPrefix);
            }
        }

        return $tranxPrefix . $transactionId;
    }


    /**
     * Generate Transaction Id For Product
     * @param $transactionId
     *
     * @return string
     */
    public function generateTransactionReference($transactionId)
    {
        return $this->generateTransactionId($transactionId);
    }


    /**
     * Generate Merchant Reference For Transaction
     * @param $transactionId
     *
     * @return string
     */
    public function generateMerchantReference($transactionId)
    {
        return $this->generateTransactionId($transactionId);
    }

    /**
     * @param $transactionId
     * @param $transactionAmount
     * @param string $gateway
     * @param array $data
     * GTPay:  gtpay_mert_id,gtpay_tranx_id,gtpay_tranx_amt,gtpay_tranx_curr,gtpay_cust_id,gtpay_tranx_noti_url
     * WebPay: tnx_ref + product_id + pay_item_id + amount + site_redirect_url + <the provided MAC key>
     *
     *
     * @return string
     * @throws UnknownPaymentGatewayException
     * @internal param $productId
     */
    public function generateTransactionHash($transactionId, $transactionAmount, $gateway = 'gtpay', $data = [])
    {
        switch ($gateway) {

//        [gtpay_mert_id,gtpay_tranx_id,gtpay_tranx_amt,gtpay_tranx_curr,gtpay_cust_id,gtpay_tranx_noti_url]
            case 'gtpay':
                $notifyUrl = route($this->getConfig($gateway, 'gtpay_tranx_noti_url'), $transactionId);
                $hashkey =  trim($this->getConfig($gateway, 'hashkey'));
                $mertId =  trim($this->getConfig($gateway, 'gtpay_mert_id'));

                $currency =  isset($data['currency']) ? (($this->getConfig($gateway, 'tranx_curr') == '$') ? '884' : '566') : trim($data['currency']);
                $customerId =  $data['customerId'];

//                $concat = $transactionId . $transactionAmount . $notifyUrl  . $hashkey;
                $concat = $mertId . $transactionId . $transactionAmount . $currency . $customerId . $notifyUrl  . $hashkey;

//                dd($concat);
                return hash('sha512', $concat);

                break;

            case 'webpay':
                return hash(
                    'sha512',
                    $this->generateTransactionId($transactionId) . $transactionId .
                    $data['payItemId'] . $transactionAmount .
                    route($this->getConfig('webpay', 'site_redirect_url')) .
                    $this->getConfig('webpay', 'hashkey')
                );

                break;

            case 'cashenvoy':
                $key = $this->getConfig('cashenvoy', 'ce_key');

                $data = $key.$transactionId. $this->toFloat($transactionAmount);
                return hash_hmac('sha256', $data, $key, false);
                break;

            default:
                throw new UnknownPaymentGatewayException;
                break;
        }
    }



    public function generateVerificationHash($tranx_id, $gateway = 'gtpay', $product_id = '4220')
    {
        switch ($gateway) {
            case 'cashenvoy':
                $key = $this->getConfig($gateway, 'ce_key');
                $mertid = $this->getConfig($gateway, 'ce_merchantid');
                $data = $key. $tranx_id. $mertid;
                return hash_hmac('sha256', $data, $key, false);

                break;

            case 'gtpay':
                return hash('sha512', $this->getConfig($gateway, 'gtpay_mert_id') . $tranx_id . $this->getConfig($gateway, 'hashkey'));
                break;

            case 'webpay':
                return hash('sha512', $product_id . $tranx_id . $this->getConfig($gateway, 'hashkey'));
                break;
        }
    }

    /**
     * @param $driver
     *
     * @throws UnknownPaymentGatewayException
     * @return string
     */
    protected function determineGatewayUrl($driver)
    {
        switch ($driver) {
            case 'gtpay':
                $gatewayUrl = $this->getConfig('gtpay', 'gatewayUrl');
                break;

            case 'webpay':
                $gatewayUrl = $this->getConfig('webpay', 'gatewayUrl')  . '/pay';
                break;

            case 'voguepay':
                $gatewayUrl = $this->getConfig('voguepay', 'gatewayUrl');
                break;

            case 'simplepay':
                $gatewayUrl = $this->getConfig('simplepay', 'gatewayUrl');
                break;

            case 'cashenvoy':
                $gatewayUrl = $this->getConfig('cashenvoy', 'gatewayUrl') . '?cmd=cepay';
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
     * @param null $gateway
     *
     * @return mixed
     */
    public function config($key  = '*')
    {
//        $key = empty($key) ? '*'
        $gateway = strtolower($this->config->get('lara-pay-ng.gateways.driver'));

        return $this->getConfig($gateway, $key);
    }

    /**
     * @param string $gateway
     * @param null|string $key
     *
     * @return array|mixed|string
     */
    protected function getConfig($gateway = '', $key = '*')
    {
        $keywithdot = '.' . $key;

        switch ($gateway) {
            case 'driver':
                return strtolower($this->config->get('lara-pay-ng.gateways.driver'));
                break;

            case 'transactionIdPrefix':
                return $this->config->get('lara-pay-ng.gateways.transactionIdPrefix');
                break;

            case 'MerchantReferencePrefix':
                return $this->config->get('lara-pay-ng.gateways.MerchantReferencePrefix');
                break;

            case 'transactionReferencePrefix':
                return $this->config->get('lara-pay-ng.gateways.transactionReferencePrefix');
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

            case 'simplepay':
                return $this->getGatewayConfig($gateway, $key, $keywithdot);
                break;

            case 'cashenvoy':
                return $this->getGatewayConfig($gateway, $key, $keywithdot);
                break;

            default:
                return 'Unknown Config Variable Requested!!';
                break;
        }
    }

    /**
     * @param $transactionId
     * @param $transactionData
     * @param $class
     * @param $buttonTitle
     *
     * @return string
     * @throws UnknownPaymentGatewayException
     * @throws UnspecifiedFieldsInTransactionData
     * @throws UnspecifiedTransactionAmountException
     */
    private function generateSubmitButtonForGTPay($transactionId, $transactionData, $class, $buttonTitle)
    {

        $transactionData = $this->allowedTransactionDataFields($transactionData, 'gtpay');


        $formId = 'PayViaGTPay';

        $gatewayUrl = $this->determineGatewayUrl('gtpay');

        $currency = null;
        $customerId = null;

        $hiddens = [ ];
        $addition = [ ];
        foreach ($transactionData as $key => $val) {
            if ($key == 'gtpay_tranx_amt') {}
            elseif ($key == 'gtpay_cust_id') {
                if(is_null($val)) throw new UnspecifiedFieldsInTransactionData('gtpay_cust_id Not Specified');

                $customerId = $val;

                $configs[] = '<input type="hidden" name="' . $key . '" value="' . $customerId . '" />' . "\n";
            }
            elseif ((substr($key, 0, 5) == 'item_' or substr($key, 0, 6) == 'price_' or substr($key, 0, 12) == 'description_') === false) {
                $hiddens[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }

        }

        foreach ($this->getConfig('gtpay') as $key => $val) {
            if ($key == 'gtpay_tranx_noti_url') {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . route($val, $transactionId) . '" />' . "\n";
            }
            elseif ($key == 'gtpay_tranx_curr') {

                $currency = ($val == '$') ? '884' : '566';
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . $currency . '" />' . "\n";
            }
            elseif (!is_null($this->getConfig('gtpay', $key)) and $key != 'gatewayUrl' and $key != 'hashkey'
                and $key != 'table') {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }

        }

        $tranxId = '<input type="hidden" name="gtpay_tranx_id" value="' . $transactionId . '" />' . "\n";

        $echodata = (isset($transactionData['gtpay_echo_data']))
            ? '<input type="hidden" name="gtpay_echo_data" value="' . $transactionData['gtpay_echo_data'] . '" />' . "\n"
            : '<input type="hidden" name="gtpay_echo_data" value="' . $transactionId . ';'. '" />' . "\n";


        if ((! isset($transactionData['gtpay_tranx_amt']) ) AND ( ! array_key_exists('price_1', $transactionData))) {
            throw new UnspecifiedTransactionAmountException;
        }

        $amount = (! isset($transactionData['gtpay_tranx_amt']) ) ? $this->sumItemPrices($transactionData) : $transactionData['gtpay_tranx_amt'];
        $amountInCoins = $this->toCoins($amount);


        $addition[] = '<input type="hidden" name="gtpay_tranx_amt" value="' . $amountInCoins . '" />' . "\n";



        $hash = '<input type="hidden" name="gtpay_hash" value="' . $this->generateTransactionHash($transactionId, $amountInCoins, $gateway = 'gtpay', ['currency' => $currency,  'customerId' => $customerId]) . '" />' . "\n";

        $addition[] = '<button type="submit"  class="' . $class . '">' . $buttonTitle . '</button>';

        $form = '<form method="POST" target="_self" action="' . $gatewayUrl . '" id="' . $formId . '">' .
            $tranxId . implode('', $configs) . implode('', $hiddens) . $hash .
            $echodata . implode('', $addition) .
            '</form>';

        return $form;
    }

    /**
     * @param $transactionId
     * @param $transactionData
     * @param $class
     * @param $buttonTitle
     *
     * @throws UnknownPaymentGatewayException
     * @throws UnspecifiedPayItemIdException
     * @throws UnspecifiedTransactionAmountException
     * @return string
     */
    private function generateSubmitButtonForWebPay($transactionId, $transactionData, $class, $buttonTitle)
    {
        $transactionData = $this->allowedTransactionDataFields($transactionData, 'webpay');

        $formId = 'PayViaWebPay';

        $gatewayUrl = $this->determineGatewayUrl('webpay');

        $hiddens = [ ];
        $addition = [ ];
        foreach ($transactionData as $key => $val) {
            $hiddens[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
        }

        foreach ($this->getConfig('webpay') as $key => $val) {
            if (!is_null($this->getConfig('webpay', $key)) and $key != 'gatewayUrl' and $key != 'hashkey') {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }
        }

        $transactionId[] = '<input type="hidden" name="txn_ref" value="' . $this->generateTransactionId($transactionId) . '" />' . "\n";
        $transactionId = '<input type="hidden" name="product_id" value="' . $transactionId . '" />' . "\n";

        if (! isset($transactionData['amount'])) {
            throw new UnspecifiedTransactionAmountException;
        }
        if (! isset($transactionData['pay_item_id'])) {
            throw new UnspecifiedPayItemIdException;
        }

        $hash = '<input type="hidden" name="hash" value="' . $this->generateTransactionHash($transactionId, $transactionData['amount'], 'webpay', $transactionData['pay_item_id']) . '" />' . "\n";


        $addition[] = '<button type="submit"  class="' . $class . '">' . $buttonTitle . '</button>';

        $form = '
            <form method="POST" action="' . $gatewayUrl . '" id="' . $formId . '">
                ' . implode('', $transactionId) . '
                ' . $transactionId . '
                ' . implode('', $configs) . '
                ' . implode('', $hiddens) . '
                ' . $hash . '
                ' . implode('', $addition) . '

            </form>
        ';

        return $form;
    }

    /**
     * @param $merchantRef
     * @param array $transactionData
     * @param string $class
     * @param string $buttonTitle
     *
     * @return string
     * @throws UnspecifiedTransactionAmountException
     */
    private function generateSubmitButtonForVoguePay($merchantRef, $transactionData, $class, $buttonTitle)
    {
        $transactionData = $this->allowedTransactionDataFields($transactionData, 'voguepay');


        $voguePayButtons = [
            'buynow_blue.png', 'buynow_red.png', 'buynow_green.png', 'buynow_grey.png', 'addtocart_blue.png',
            'addtocart_red.png', 'addtocart_green.png', 'addtocart_grey.png', 'checkout_blue.png',
            'checkout_red.png', 'checkout_green.png', 'checkout_grey.png', 'donate_blue.png', 'donate_red.png',
            'donate_green.png', 'donate_grey.png', 'subscribe_blue.png', 'subscribe_red.png',
            'subscribe_green.png', 'subscribe_grey.png', 'make_payment_blue.png', 'make_payment_red.png',
            'make_payment_green.png', 'make_payment_grey.png',
        ];

        $formId = 'PayViaVoguePay';

        $gatewayUrl = $this->determineGatewayUrl('voguepay');

        $hiddens = [ ];
        $configs = [ ];
        $addition = [ ];

        foreach ($transactionData as $key => $val) {
            if ($key != 'merchant_ref') {
                $hiddens[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }
        }

        foreach ($this->getConfig('voguepay') as $key => $val) {
            if ($key == 'notify_url' or $key == 'success_url' or $key == 'fail_url') {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . route($val, $merchantRef) . '" />' . "\n";
            } elseif (!is_null($this->getConfig('voguepay', $key)) and $key != 'submitButton' and $key != 'table') {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }
        }

        if ((isset($transactionData['total']) or array_key_exists('price_1', $transactionData)) === false) {
            throw new UnspecifiedTransactionAmountException;
        }

        $merchantRef = '<input type="hidden" name="merchant_ref" value="' . $merchantRef . '" />' . "\n";

        $defaultButton = $this->getConfig('voguepay', 'submitButton');

        $addition[] = in_array($defaultButton, $voguePayButtons)
            ? '<input type="image"  src="//voguepay.com/images/buttons/' . $defaultButton . '" alt="Submit">'
            : '<input type="submit"  class="' . $class . '">' . $buttonTitle . '</input>';

        $form = '<form method="POST" action="' . $gatewayUrl . '" id="' . $formId . '">' .
            implode('', $configs) . $merchantRef . implode('', $hiddens) . implode('', $addition) .
            '</form>';

        return $form;
    }

    /**
     * @param $transactionRef
     * @param array $transactionData
     * @param string $class
     * @param string $buttonTitle
     *
     * @return string
     * @throws UnspecifiedTransactionAmountException
     */
    private function generateSubmitButtonForCashEnvoy($transactionRef, $transactionData, $class, $buttonTitle)
    {
        $formId = 'PayViaCashEnvoy';

        $gatewayUrl = $this->determineGatewayUrl('cashenvoy');

        $transactionData = $this->allowedTransactionDataFields($transactionData, 'cashenvoy');

        $hiddens = [ ];
        $configs = [ ];
        $addition = [ ];


        foreach ($transactionData as $key => $val) {
            if ($key == 'ce_amount') {}
            elseif ((substr($key, 0, 5) == 'item_' or substr($key, 0, 6) == 'price_' or substr($key, 0, 12) == 'description_') === false) {
                $hiddens[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }
        }

        foreach ($this->getConfig('cashenvoy') as $key => $val) {
            if ($key == 'ce_notifyurl') {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . route($val, $transactionRef) . '" />' . "\n";
            } elseif (!is_null($this->config($key)) and $key != 'gatewayUrl' and $key != 'ce_key'
                and $key != 'table' and $key != 'icon') {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }
        }


        if ((isset($transactionData['ce_amount']) or array_key_exists('price_1', $transactionData)) === false) {
            throw new UnspecifiedTransactionAmountException;
        }

        $amount = (! isset($transactionData['ce_amount']) ) ? $this->sumItemPrices($transactionData) : $transactionData['ce_amount'];


        $addition[] = '<input type="hidden" name="ce_amount" value="' . $this->toFloat($amount) . '" />' . "\n";

        $transRef = '<input type="hidden" name="ce_transref" value="' . $transactionRef . '" />' . "\n";

        $signature = '<input type="hidden" name="ce_signature" value="' . $this->generateTransactionHash($transactionRef, $amount, 'cashenvoy') . '" />' . "\n";


        $addition[] =  '<button type="submit"  class="' . $class . '">' . $buttonTitle . '<span><img src="' . $this->config('icon') . '"></span></button>';

        $form = '<form method="POST" target="_self" name="ce" action="' . $gatewayUrl . '" id="' . $formId . '">' .
            implode('', $configs) . $transRef . implode('', $hiddens) . $signature .
            implode('', $addition) .
            '</form>';

        return $form;
    }


    /**
     * @param $customid
     * @param array $transactionData
     * @param string $class
     * @param string $buttonTitle
     *
     * @return string
     */
    private function generateSubmitButtonForSimplePay($customid, $transactionData, $class, $buttonTitle)
    {
        $transactionData = $this->allowedTransactionDataFields($transactionData, 'simplepay');

        $simplePayButtons = [
            'simplepaylogoescrow.gif', 'simplepaylogo.gif', 'simplepaysubscribe.gif',
            'spaccepted.png', 'simplepaydonatenow.gif',
        ];

        $formId = 'PayViaSimplePay';

        $gatewayUrl = $this->determineGatewayUrl('simplepay');

        $hiddens = [ ];
        $configs = [ ];
        $addition = [ ];

//        <!-- SimplePay PAYMENT FORM -->
//        <form method=post action=https://simplepay4u.com/process.php>

//        <input type=hidden name=period value="--DURATION-DAYS--">
//        <input type=hidden name=trial value="--TRIAL-PERIOD-DAYS--">
//        <input type=hidden name=setup value="--SETUP-FEES--">
//        <input type=hidden name=tax value="--TAX-FEES--">
//        <input type=hidden name=shipping value="--SHIPPING-FEES--">

//        <input type=hidden name=CMAccountid value="11221313">

//        <input type=hidden name=member value="useraccount@simplepay4u.com">
//        <input type=hidden name=escrow value="N">
//        <input type=hidden name=action value="payment">
//        <input type=hidden name=price value="100">
//        <input type=hidden name=quantity value="1">
//        <input type=hidden name=ureturn value="http://www.mydomain.com/success.php">
//        <input type=hidden name=unotify value="http://www.mydomain.com/notify.php">
//        <input type=hidden name=ucancel value="http://www.mydomain.com/failure.php">
//        <input type=hidden name=comments value="Payment Received for Service">
//        <input type=hidden name=customid value="SP93104">
//        <input type=hidden name=freeclient value="N">
//        <input type=hidden name=nocards value="N">
//        <input type=hidden name=giftcards value="Y">
//        <input type=hidden name=chargeforcard value="Y">
//        <input type=hidden name=site_logo value="http://www.mydomain.com/images/logo.gif">
//        <input type=image src="http://www.mydomain.com/images/submit.gif">
//        </form>

        foreach ($transactionData as $key => $val) {
            if ($key == 'member') {}
            elseif ($key == 'freeclient' or $key == 'escrow' or $key == 'giftcards' or $key == 'chargeforcard' or $key == 'nocards') {
                $hiddens[] = '<input type="hidden" name="' . $key . '" value="' . (($val === false) ? 'N' : 'Y')   . '" />' . "\n";
            } elseif ((substr($key, 0, 5) == 'item_' or substr($key, 0, 6) == 'price_' or substr($key, 0, 12) == 'description_') === false) {
                $hiddens[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }
        }

        foreach ($this->getConfig('simplepay') as $key => $val) {
            if ($key == 'unotify' or $key == 'ucancel' or $key == 'ureturn') {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . route($val, $customid) . '" />' . "\n";
            } elseif (!is_null($this->getConfig('simplepay', $key)) and $key != 'submitButton' and $key != 'gatewayUrl' and $key != 'table') {
                $configs[] = '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
            }

        }

        $customid = '<input type="hidden" name="customid" value="' . $customid . '" />' . "\n";

        $defaultButton = $this->getConfig('simplepay', 'submitButton');

        $addition[] = in_array($defaultButton, $simplePayButtons)
            ? '<input type="image"  src="https://simplepay4u.com/hlib/images/client_img/' .
            $defaultButton . '" alt="Submit">'

            : '<input type="submit"  class="' . $class . '">' . $buttonTitle . '</input>';

        $form = '<form method="POST" action="' . $gatewayUrl . '" id="' . $formId . '">
                    ' . implode('', $configs) . '
                    ' . $customid . '
                    ' . implode('', $hiddens) . '
                    ' . implode('', $addition) . '
                </form>';

        return $form;
    }


    /**
     * @param $gateway
     * @param $key
     * @param $keywithdot
     *
     * @return mixed|string
     */
    private function getGatewayConfig($gateway, $key, $keywithdot)
    {
        if ($key == '*') {
            return $this->config->get('lara-pay-ng.gateways.' . $gateway);
        }

        if (! array_key_exists($key, $this->config->get('lara-pay-ng.gateways.' . $gateway))) {
            return 'Trying to get an Unknown ' . $gateway . ' Config';
        }

        return $this->config->get('lara-pay-ng.gateways.'. $gateway . $keywithdot);
    }

    protected function sumItemPrices($transactionData)
    {
        $total = 0;

        foreach ($transactionData as $key => $value) {
            if (strpos($key, 'price_') === 0) {
                $total += $value;
            }
        }

        return $this->toFloat($total);
    }

    /**
     * @param $result
     *
     * @return array
     */
    protected function collateResponse($result)
    {
        switch ($result) {
            case (isset($result->r_gtpay_tranx_id)):
                return [
                    'status'         => $result->gtpay_response_description,
                    'transaction_id' => $result->r_gtpay_tranx_id,
                    'items'          => $result->items,
                    'merchant_ref'   => (!empty($result->gtpay_merchant_ref)) ? $result->gtpay_merchant_ref : 'N/A',
                    'amount'         => ($result->gtpay_tranx_curr == '844')
                        ? $this->inDollars($result->r_gtpay_amount)
                        : $this->inNaira($result->r_gtpay_amount),
                    'customer_id'    => $result->gtpay_cust_id,
                    'payer_id'       => $result->gtpay_cust_id
                ];
                break;

            case (isset($result->v_transaction_id)):
                return [
                    'status'         => $result->status,
                    'transaction_id' => $result->v_transaction_id,
                    'items'          => $result->items,
                    'merchant_ref'   => $result->merchant_ref,
                    'amount'         => $this->inNaira($result->v_total_paid),
                    'customer_id'    => $result->v_email,
                    'payer_id'       => $result->v_email
                ];
                break;

            case (isset($result->ce_transref)):

                return [
                    'status'         => $result->response_description,
                    'items'          => $result->items,
                    'transaction_id' => isset($result->transaction_id) ? $result->transaction_id : null,
                    'merchant_ref'   => $result->ce_transref,
                    'amount'         => ($result->response_code == 'C00') ? $this->inNaira($result->amount) : $this->inNaira(0.00),
                    'customer_id'    => $result->ce_customerid,
                    'payer_id'       => $result->ce_customerid
                ];
                break;

            case (isset($result->s_transaction_id)):
                return [
                    'status'         => $result->status,
                    'transaction_id' => $result->s_transaction_id,
                    'merchant_ref'   => $result->customid,
                    'amount'         => $result->s_total,
                    'customer_id'    => $result->s_buyer
                ];
                break;

//            case (isset($result->v_transaction_id)):
//                return [
//                    'status'         => $result->gtpay_response_description,
//                    'transaction_id' => $result->v_transaction_id,
//                    'merchant_ref'   => $result->gtpay_merchant_ref,
//                    'amount'         => $result->r_gtpay_amount,
//                    'customer_id'    => $result->gtpay_cust_id
//                ];
//                break;
        }
    }

    /**
     * @param null $gateway
     *
     * Get All Transactions
     *
     * @return mixed
     */
    public function getAllTransactions($gateway = null)
    {
        $table = is_null($gateway) ? $this->config('table') : $this->getConfig($gateway, 'table');

        return $this->dataRepository->getAllTransactionsFrom($table);
    }

    /**
     * @param null $gateway
     *
     * Get All Successful Transactions
     *
     * @return mixed
     */
    public function getSuccessfulTransactions($gateway = null)
    {
        $table = is_null($gateway) ? $this->config('table') : $this->getConfig($gateway, 'table');

        return $this->dataRepository->getAllSuccessfulTransactionsFrom($table);
    }

    /**
     * @param null $gateway
     *
     * Get All Failed Transactions
     *
     * @return mixed
     */
    public function getFailedTransactions($gateway = null)
    {
        $table = is_null($gateway) ? $this->config('table') : $this->getConfig($gateway, 'table');

        return $this->dataRepository->getAllFailedTransactionsFrom($table);
    }

    private function allowedTransactionDataFields($transactionData, $gateway)
    {

        switch($gateway) {

            case 'gtpay':

                $allowedFields = [
                    'gtpay_cust_name', 'gtpay_tranx_amt', 'gtpay_tranx_memo', 'gtpay_cust_id',
                    'gtpay_gway_name', 'gtpay_tranx_curr', 'gtpay_gway_first', 'gtpay_echo_data',
                    'gtpay_tranx_id'
                ];

                return $this->extractNeededTransactionData($transactionData, $allowedFields);

                break;

            case 'simplepay':

                $allowedFields = [
                    'period', 'trial', 'setup', 'tax', 'shipping', 'escrow', 'action',
                    'price', 'quantity', 'comments', 'customid', 'freeclient', 'nocards',
                    'giftcards', 'chargeforcard', 'site_logo', 'payer_id'
                ];

                return $this->extractNeededTransactionData($transactionData, $allowedFields);

                break;

            case 'voguepay':

                $allowedFields = [
                    'merchant_ref', 'memo', 'developer_code', 'store_id', 'total',
                    'recurrent', 'interval'
                ];

                return $this->extractNeededTransactionData($transactionData, $allowedFields);

                break;

            case 'webpay':

                $allowedFields = [
                    'period', 'trial', 'setup', 'tax', 'shipping', 'escrow', 'action',
                    'price', 'quantity', 'comments', 'customid', 'freeclient', 'nocards',
                    'giftcards', 'chargeforcard', 'site_logo'
                ];

                return $this->extractNeededTransactionData($transactionData, $allowedFields);

                break;

            case 'cashenvoy':

                $allowedFields = [
                    'ce_transref', 'ce_amount', 'ce_customerid', 'ce_memo', 'ce_window', 'ce_type'
                ];

                return $this->extractNeededTransactionData($transactionData, $allowedFields);

                break;

            default:
                throw new UnknownPaymentGatewayException;
                break;

        }
    }

    /**
     * @param $transactionData
     * @param $allowedFields
     *
     * @return mixed
     */
    private function extractNeededTransactionData($transactionData,$allowedFields) {

        $redefinedTransactionData = [];

        $generalFields = ['price_', 'description_', 'item_'];

        foreach ($transactionData as $key => $data) {
            if (in_array($key, $allowedFields)) {
                $redefinedTransactionData[$key] = $data;
            }

            if (starts_with($key, $generalFields)) {
                $redefinedTransactionData[$key] = $data;
            }
        }

        return $redefinedTransactionData;
    }

}
