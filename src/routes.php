<?php

$successUrl = \Config::get('lara-pay-ng::gateways.routes.success_route');
$successName = \Config::get('lara-pay-ng::gateways.routes.success_route_name');

$failureUrl = \Config::get('lara-pay-ng::gateways.routes.failure_route');
$failureName = \Config::get('lara-pay-ng::gateways.routes.failure_route_name');


function determineNotificationUrl()
{

    switch (\Config::get('lara-pay-ng::gateways.driver'))
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


//Route::get('/' . $successUrl, ['as' => $successName,'uses' => '\Dammyammy\LaraPayNG\PaymentController@success']);
//Route::get('/' . $failureUrl, ['as' => $failureName,'uses' => '\Dammyammy\LaraPayNG\PaymentController@failed']);
Route::post('/notification', ['as' => 'notification','uses' => '\Dammyammy\LaraPayNG\PaymentController@processPayment']);
