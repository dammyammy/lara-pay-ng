
*Supports Multiple Gateways with a single API approach, meaning Integration is the same across board in code.*

*Note:* Do Not Use In Production, Currently being developed to be compatible with the latest version of Laravel.

The Aim is to Integrate as many Payment Gateways As Possible.

# Requirements


# Currently Supported
- VoguePay
- GTPay
- CashEnvoy
- SimplePay


# Gateways Currently In development
- WebPay


# Installation

Simply Run

```shell
    composer require dammyammy/lara-pay-ng
```

Next Add the Service Provider into your Providers Array in config/app.php

```php
    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',

        ....

        'LaraPayNG\Providers\LaraPayNGServiceProvider',
    ],
```

Next Publish All Package Files

```shell
php artisan vendor:publish
```

This Would create the following:

1. A *PaymentController* in *app/Http/Controllers/PaymentController.php*
- Note: This is a working Sample, you may need to change Namespace to match your Projects namespace
2. *Migrations* would be published to folder database/migrations
3. 4 Views, matching the Controller
- one with a simulated checkout out button
- one is the proceed to pay page (Pay Now Button)
- one is a success notification Url, Showing a Successful Transaction
- one is a failure notification Url, Showing a Failed Transaction

Next Add the following to your routes page in app/Http/routes.php and edit as you seem fit, But be sure specified values match Your Config file

```php
    Route::get('orders',  [
        'as' => 'orders',
        'uses' => 'PaymentController@orders'
    ]);

    Route::get('checkout',  [
        'as' => 'checkout',
        'uses' => 'PaymentController@checkout'
    ]);

    $successUrl = config('lara-pay-ng.gateways.routes.success_route');
    $successName = config('lara-pay-ng.gateways.routes.success_route_name');

    $failureUrl = config('lara-pay-ng.gateways.routes.failure_route');
    $failureName = config('lara-pay-ng.gateways.routes.failure_route_name');

    Route::post('/' . $successUrl . '/{mert_id}', [
        'as' => $successName,
        'uses' => 'PaymentController@success'
    ]);

    Route::post('/' . $failureUrl . '/{mert_id}', [
        'as' => $failureName,
        'uses' => 'PaymentController@failed'
    ]);

    Route::post('/payment-notification/{mert_id}', [
        'as' => 'payment-notification',
        'uses' => 'PaymentController@notification'
    ]);
```


# Remember to Disallow CSRF Token Verification for your payment routes

This is perhaps one of the use cases in which u need to absolutely do this. If this is not done, When a Transaction Id is been sent back your site would throw a TokenMismatchException, as the Gateway Provider is posting back, and doesn't have a token Generated from your app.

Use the appropriate route endpoints if you did change the default names and urls.

```php
<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
    * The URIs that should be excluded from CSRF verification.
    *
    * @var array
    */
    protected $except = [
        'transaction-failed/*',
        'transaction-successful/*',
        'payment-notification/*',

    ];
}
```

Next Migrate the Package Tables.

```shell
php artisan migrate
```

Next, Test the default Controller by visiting /Orders and follow through.

The Important thing to note is, to Inject the Payment Functionality into a controller, all you need do is to Inject the PaymentGatewayManager Class into that controller, and you instantly gain access to all the methods provided by the facades below.

```php
use LaraPayNG\Exceptions\UnknownPaymentGatewayException;
use LaraPayNG\Managers\PaymentGatewayManager;

class PaymentController extends Controller
{
    /**
    * @var PaymentGateway
    */
    private $paymentGateway;


    /**
    * @param PaymentGatewayManager $paymentGateway
    */
    public function __construct(PaymentGatewayManager $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    ....

}
```

# Facades

Facades | Namespace | When To Use
--------| ----------| -----------
Pay:: | \LaraPayNG\Facades\Pay | Use If You want To Swap Gateways Through Config.
GTPay:: | \LaraPayNG\Facades\GTPay | Use If You Specifically want the GTPay Gateway Implementation.
WebPay:: | \LaraPayNG\Facades\WebPay | Use If You Specifically want the WebPay Gateway Implementation.
VoguePay:: | \LaraPayNG\Facades\VoguePay | Use If You Specifically want the VoguePay Gateway Implementation.
CashEnvoy:: | \LaraPayNG\Facades\CashEnvoy | Use If You Specifically want the CashEnvoy Gateway Implementation.
SimplePay:: | \LaraPayNG\Facades\SimplePay | Use If You Specifically want the SimplePay Gateway Implementation.


## Methods Available Via Facades / PaymentGatewayManager Injection

Method | What it is Meant For
-------|---------------------
button($transactionId, $transactionData, $class, $buttonTitle) |  To create Pay Now Button For Set Gateway In a View.
logTransaction($transactionData) | To Store Data Transaction Being Made for future Reference.
receiveTransactionResponse($transactionId, $mertId) | To Get Transaction Response back from Gateway.
logResponse($transactionData) | To Store Transaction Response from Gateway.
getDefaultDriver() | To get Default Payment Gateway Driver At Runtime.
with($name) | To set Default Payment Gateway Driver At Runtime..
config($key) | Access Config Off Set Default Payment Gateway Driver.


# Events

Event Name | Full Event Namespace | When It is Thrown
-----------|----------------------|------------------
TransactionSuccessful | \LaraPayNG\Events\TransactionSuccessful | When A Transaction is deemed successful
TransactionUnsuccessful | \LaraPayNG\Events\TransactionUnsuccessful | When A Transaction is deemed unsuccessful|

Throwing this Events are optional, but helps serve as a hook for how you handle Completed Transactions.Take these Example Scenarios When you would Like to

1. Send A customer an Email to notify them of their recent Transaction Status.
2. You want to Set a person's account to active in the User's Table after a successful Transaction.
3. You Want to add Paying Customer's to your Paying Customer's Mailer List.

The possibilities are endless, All you have to do is to Pull in the \LaraPayNG\Traits\DetermineTransactionStatus Trait and call the dispatchAppropriateEvents($result); method within your controller. e.g.

```php
public function notification($mert_id, Request $request) {
    $result = $this->handleTransactionResponse($mert_id, $request);
    $this->dispatchAppropriateEvents($result);
    return $this->determineViewToPresent($result);
}
```

Then Simply Create Event Listeners in app/Providers/EventServiceProvider.php

```php
<?php
namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
    * The event listener mappings for the application.
    *
    * @var array
    */
    protected $listen = [
        'LaraPayNG\Events\TransactionSuccessful' => [
            // 'App\Listeners\SendEmailForSuccessfulTransactions',
            // 'App\Listeners\ActivateUser',
        ],
        'LaraPayNG\Events\TransactionUnsuccessful' => [
            // 'App\Listeners\DeactivateUser',
        ],
    ];

    ....

}
```

Finally, Create Your Listener Implementations Knowing you have access to an array result containing values needed. An example implementation is written below:

 > File: app/Listeners/ActivateUser.php

```php
<?php
namespace App\Listeners;

use LaraPayNG\Events\TransactionSuccessful;
use App\User;

class ActivateUser
{
    /**
    * Handle the event.
    *
    * @param  ActivateUser  $event
    * @return void
    */
    public function handle(ActivateUser $event) {
         // All Values Below are available
         // $event['status'],
         // $event['items'],
         // $event['transaction_id'],
         // $event['merchant_ref'],
         // $event['amount'],
         // $event['customer_id'],
         // $event['payer_id'],

        $user = User::find($event['customer_id']);
        $user->activate = true;
        $user->save();
        return true;
    }
}
```


# Exceptions
All Exceptions Exist Under This Namespace;

```php
    namespace \LaraPayNG\Exceptions;
```

Exceptions | When It is Thrown
-----------|---------------------
UnknownPaymentGatewayException | If set Driver for Default Gateway is unknown or unsupported.
UnspecifiedTransactionAmountException | If amount(WebPay) / gtpay_tranx_amt(GTPay) is not in $transactionData.
UnspecifiedPayItemIdException |  If pay_item_id is unspecifed in $transactionData array (GTPay).
PaymentGatewayVerificationFailedException | If Hash Calculation is Wrong During Verification (Applies to GTPay/WebPay Gateway ).


# Commands
There is a Command to Help Clear Stale Records That have been logged within a set number of days. To Use Simply Type in your terminal

```shell
# php artisan lara-pay-ng:purge-database gatewayname --days=3 --with-failed=false
php artisan lara-pay-ng:purge-database
```

You can Pass the gatewayname attribute to the Command eg. gtpay, voguepay. This is particularly useful for Multi-gateway Setups.

In the event of not passing the option, the default gateway driver from set config would be used.

It also can be passed a *--with-failed* option which accepts true/false. True means all Transactions that failed should be included in the deletion, as well as a *--days* option specifying how many days back data is considered old.


# Tips

Add Methods Like These to Your Helpers.php File then Autoload It In Your composer.json

```php
public function generateTransactionData($dessert, $transactionId) {
    return 'name=' . $dessert->present()->name . ';pre=' . $dessert->present()->buyPrice . ';buyer=' . currentUserName() . '; transactionId=' . $transactionId;
}

public function generateTransactionMemo($product) {
    return 'Name: ' . $product->name . '; Price: ' . $product->price . '; Buyer: ' . Auth::user()->email;
}
```

then You can easily Do

```php
$product = Product::get('2');

$transactionData = [
    'ce_amount' => $product->price,
    'ce_memo' => generateTransactionMemo($product),

    ...
];

CashEnvoy::button($product->id, $transactionData, 'btn btn-success', '<i class="fa fa-currency"></i> Pay');
```

# TODO

- Refactor Code
- Ability to Handle Transaction From Start to Finish
- Back everything up with Tests.
