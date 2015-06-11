Do Not Use, Currently been developed to be compatible with the latest version of Laravel. 

Aiming to Integrate as much Payment Gateways As Possible

TODO
- Refactor Code
- Create a Simple Straightforward API
- Ability to Handle Transaction From Start to Finish
- Back everything up with Tests.
 
Gateways Currently being Looked at
- CashEnvoy
- SimplePay


Currently Supported (Would be perfected soon)
- GTPay
- WebPay
- VoguePay





# Facades


    Facades         Namespace                                   When To Use Which Facade                        
    
    Pay::           \Dammyammy\LaraPayNG\Facades\Pay            Use If You want To Swap Gateways Through Config.
    GTPay::         \Dammyammy\LaraPayNG\Facades\GTPay          Use If You Specifically want GTPay Gateway.     
    WebPay::        \Dammyammy\LaraPayNG\Facades\WebPay         Use If You Specifically want WebPay Gateway.    
    VoguePay::       \Dammyammy\LaraPayNG\Facades\VoguePay      Use If You Specifically want VoguePay Gateway.  
    




# Methods Available Via Facades


    Method                                                          What it is Meant For                        
    
    getDefaultDriver()                                              To get Default Payment Gateway Driver At Runtime.
    setDefaultDriver($name)                                         To set Default Payment Gateway Driver At Runtime..     
    config($key)                                                    Access Config Off Set Driver Gateway.    
    buyButton($productId, $transactionData, $class, $buttonTitle)   To create Pay Now Button For Set Gateway.
    processTransaction($transactionData)                            To Process Transaction Being Made.
    
    
# Exceptions
    All Exceptions Exist Under This Namespace;;
    
    ```php
    
        namespace \Dammyammy\LaraPayNG\Exceptions;
    ```

    Exceptions                                  When It is Thrown                        
    
    UnknownPaymentGatewayException              If set Driver for Default Gateway is unknown or unsupported.
    UnspecifiedTransactionAmountException       If amount(WebPay) | gtpay_tranx_amt(GTPay) is not in $transactionData.
    UnspecifiedPayItemIdException               If pay_item_id is unspecifed in $transactionData array (GTPay)
    PaymentGatewayVerificationFailedException   If Hash Calculation is Wrong During Verification ( Applies to GTPay/WebPay Gateway ).
    
    
# Tips

        Add Methods Like These to Your Helpers.php File then Autoload It In Your composer.json
        
     ```php
     
         public function generateTransactionData($dessert, $transactionId)
         {
             return 'name=' . $dessert->present()->name . ';pre=' . $dessert->present()->buyPrice
             . ';buyer=' . currentUserName() . '; transactionId=' . $transactionId;
         }
     
     
     
     
         public function generateTransactionMemo($product)
         {
             return 'Name: ' . $product->name . '; Price: ' . $product->price
             . '; Buyer: ' . Auth::user()->email;
         }
     ```
     
     then You can easily Do
     
     ```php
        $product = Product::get('2');
        
        $transactionData = [
            'amount' => $product->price,
            'memo' => generateTransactionMemo($product),
            ...
        ];
        
        WebPay::buyButton($product->id, $transactionData, 'btn btn-success', '<i class="fa fa-currency"></i> Pay Now');
        
     ```   
        
        