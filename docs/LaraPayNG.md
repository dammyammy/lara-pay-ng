# Facades


    Facades         Namespace                                   When To Use Which Facade                        
    
    Pay::           \Dammyammy\LaraPayNG\Facades\Pay            Use If You want To Swap Gateways Through Config.
    GTPay::         \Dammyammy\LaraPayNG\Facades\GTPay          Use If You Specifically want GTPay Gateway.     
    WebPay::        \Dammyammy\LaraPayNG\Facades\WebPay         Use If You Specifically want WebPay Gateway.    
    VoguePay::       \Dammyammy\LaraPayNG\Facades\VoguePay      Use If You Specifically want VoguePay Gateway.  
    




# Methods Available Via Facades


    Method                                              What it is Meant For                        
    
    getDefaultDriver()                                  To get Default Payment Gateway Driver At Runtime.
    setDefaultDriver($name)                             To set Default Payment Gateway Driver At Runtime..     
    config($key)                                        Access Config Off Set Driver Gateway.    
    createPayButton($transactionData, $class, $value)   To create Pay Now Button For Set Gateway.
    processTransaction($transactionData)                To Process Transaction Being Made.
    
    
# Exceptions
    All Exceptions Exist Under This Namespace;;
    
    ```php
    
        namespace \Dammyammy\LaraPayNG\Exceptions;
    ```

    Exceptions                                  When It is Thrown                        
    
    UnknownPaymentGatewayException              If set Driver for Default Gateway is unknown or unsupported.
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
        
        