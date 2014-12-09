<?php


namespace Dammyammy\LaraPayNG\Support;



use Dammyammy\LaraPayNG\Exceptions\UnknownPaymentGatewayException;
use Illuminate\Config\Repository;

class Helpers {

    /**
     * @var Repository
     */
    private $config;

    /**
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function createPayButton($transactionData, $class = '')
    {
        return $this->generateSubmitButton($transactionData);
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
     * Adapter use this method to building a hidden form.
     *
     * @param array $attrs
     * @param string $class
     *
     * @throws UnknownPaymentGatewayException
     * @internal param string $formid
     * @internal param string $method
     * @return string HTML
     */
    protected  function generateSubmitButton($attrs = [], $class = '')
    {

        $driver = $this->config->get('lara-pay-ng::gateways.driver');
        $formId =  'payvia' . $driver;

        $gatewayUrl = $this->determineGatewayUrl($driver);

        $hiddens = [];
        $addition = [];
        foreach ($attrs as $key => $val)
        {
            $hiddens[] = '<input type="hidden" name="'.$key.'" value="'.$val.'" />' . "\n";
        }
        if ($driver == 'voguepay')
        {
            $addition[] = '<p><input type="image"  src="' . $this->config->get('lara-pay-ng::gateways.voguepay.submitButton') . '" alt="Submit"></p>';
        }
        else
        {
            $addition[] = '<p><input type="submit"  class="' . $class . '" value="Pay Now"></p>';
        }
        $form = '
            <form method="POST" action="'.$gatewayUrl.'" id="'. $formId .'">
                '.implode('', $hiddens).'
                '.implode('', $addition).'
            </form>
        ';
        return $form;
    }

    /**
     * @param $productId
     *
     * @return string
     */
    public function generateTransactionId($productId)
    {
        return $this->config->get('lara-pay-ng::gateways.transactionIdPrefix') . $productId;
    }

    /**
     * @param $productId
     * @param $transactionAmount
     * @param null $payItemId
     * @param string $gateway
     *
     * GTPay:  gtpay_tranx_id + gtpay_tranx_amt + gtpay_tranx_noti_url + hashkey
     * WebPay: tnx_ref + product_id + pay_item_id + amount + site_redirect_url + <the provided MAC key>
     * VoguePay:
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
                            $this->config->get('lara-pay-ng::gateways.gtpay.tranx_noti_url') .
                            $this->config->get('lara-pay-ng::gateways.gtpay.hashkey'),
                            false
                        );

                break;

            case 'webpay':
                return hash(
                            'sha512',
                            $this->generateTransactionId($productId) . $productId .
                            $payItemId . $transactionAmount .
                            $this->config->get('lara-pay-ng::gateways.webpay.site_redirect_url') .
                            $this->config->get('lara-pay-ng::gateways.webpay.hashkey'),
                            false
                        );

                break;

            case 'voguepay':
                # code...
                break;

            default:
                throw new UnknownPaymentGatewayException;
                break;
        }

    }


//    public function generateTransactionMemo($dessert)
//    {
//        return 'Name: ' . $dessert->present()->name . '; Price: ' . $dessert->present()->buyPrice
//        . '; Buyer: ' . currentUserName();
//    }





    public function generateVerificationHash($tranx_id, $gateway = 'gtpay', $product_id = '4220')
    {
        if ($gateway == 'webpay')
        {
//     productid, transactionreference and your hash key
//       $product_id = substr($tranx_id, strpos($tranx_id, 'D') + 1);

            return hash('sha512', $product_id . $tranx_id . $this->config->get('settings.payment.webpay.hashkey'), false);
        }

//    mertid + tranxid + hashkey
        return hash('sha512', $this->config->get('services.payment.gtpay.mert_id') . $tranx_id . $this->config->get('settings.payment.gtpay.hashkey'), false);
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
                $gatewayUrl = $this->config->get('lara-pay-ng::gateways.gtpay.gatewayUrl');
                break;

            case 'webpay':
                $gatewayUrl = $this->config->get('lara-pay-ng::gateways.webpay.gatewayUrl');
                break;

            case 'voguepay':
                $gatewayUrl = $this->config->get('lara-pay-ng::gateways.voguepay.gatewayUrl');
                break;

            case 'default':
                throw new UnknownPaymentGatewayException;
                break;
        }

        return $gatewayUrl;
    }

//    public function generateTransactionData($dessert, $transactionId)
//    {
//        return 'name=' . $dessert->present()->name . ';price=' . $dessert->present()->buyPrice
//        . ';buyer=' . currentUserName() . '; transactionId=' . $transactionId;
//    }
} 