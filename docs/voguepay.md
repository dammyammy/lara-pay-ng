# Introduction:

## Vogue Pay Integration
------------------------

> [Link to VoguePay Developers Docs](https://voguepay.com/developers "VoguePay's Homepage")



Integrate VoguePay Via Simple HTML Form
Follow the steps below to manually integrate VoguePay into your website. When you're done, you will have added a VoguePay button and supporting code to your website so that customers can click to place orders through VoguePay.

Create HTML FORM
Use the sample form below and the explanation that follows to create your own html form.

####Sample HTML Form 1
----------------------

```
    
    <form method='POST' action='https://voguepay.com/pay/'>
    
        <input type='hidden' name='v_merchant_id' value='qa331322179752' />
        <input type='hidden' name='merchant_ref' value='234-567-890' />
        <input type='hidden' name='memo' value='Bulk order from McAckney Web Shop' />
        
        <input type='hidden' name='item_1' value='Face Cap' />
        <input type='hidden' name='description_1' value='Blue Zizi facecap' />
        <input type='hidden' name='price_1' value='2000' />
        
        <input type='hidden' name='item_2' value='Laban T-shirt' />
        <input type='hidden' name='description_2' value='Green XXL' />
        <input type='hidden' name='price_2' value='3000' />
        
        <input type='hidden' name='item_3' value='Black Noni Shoe' />
        <input type='hidden' name='description_3' value='Size 42' />
        <input type='hidden' name='price_3' value='8000' />
        
        <input type='hidden' name='developer_code' value='pq7778ehh9YbZ' />
        <input type='hidden' name='store_id' value='25' />
        
        <input type='hidden' name='total' value='13000' />
        
        <input type='image' src='http://voguepay.com/images/buttons/buynow_blue.png' alt='Submit' />
    
    </form>
```




####Sample HTML Form 2
-----------------------

``` 

    <form method='POST' action='https://voguepay.com/pay/'>
    
        <input type='hidden' name='v_merchant_id' value='qa331322179752' />
        <input type='hidden' name='merchant_ref' value='234-567-890' />
        <input type='hidden' name='memo' value='Membership subscription for music club' />
        
        <input type='hidden' name='recurrent' value='true' />
        <input type='hidden' name='interval' value='30' />
        
        <input type='hidden' name='developer_code' value='pq7778ehh9YbZ' />
        <input type='hidden' name='store_id' value='25' />
        
        <input type='hidden' name='total' value='13000' />
        
        <input type='image' src='http://voguepay.com/images/buttons/buynow_blue.png' alt='Submit' />
        
    </form>
```


#### Sample HTML Form 3
-----------------------

``` 

    <form method='POST' action='https://voguepay.com/pay/'>
    
        <input type='hidden' name='v_merchant_id' value='qa331322179752' />
        <input type='hidden' name='merchant_ref' value='234-567-890' />
        <input type='hidden' name='memo' value='Bulk order from McAckney Web Shop' />
        <input type='hidden' name='notify_url' value='http://www.mydomain.com/notification.php' />
        <input type='hidden' name='success_url' value='http://www.mydomain.com/thank_you.html' />
        <input type='hidden' name='fail_url' value='http://www.mydomain.com/failed.html' />
        <input type='hidden' name='developer_code' value='pq7778ehh9YbZ' />
        <input type='hidden' name='store_id' value='25' />
        <input type='hidden' name='total' value='13000' />
        <input type='image' src='http://voguepay.com/images/buttons/buynow_blue.png' alt='Submit' />
        
    </form>
```
### Form Explanation
--------------------

<table class="table">
   <tbody>
     <tr>
     <th>Attributes</th><th>Value</th><th>Explanation</th></tr>
		<tr><td>
		method
		</td><td>POST</td><td>Only the POST method is accepted.</td>
		</tr><tr><td>
		action*</td><td>
		https://voguepay.com/pay/</td><td>
		Form must submit to this url for production environment</td></tr>
		</tbody>
</table>


### Elements
------------

<table class="table">
		<tbody>
            <tr><th>Name</th><th>Value</th><th>Explanation</th></tr>
            <tr><td>v_merchant_id<span class="red">*</span></td><td>Your Voguepay Merchant ID</td><td>Can be found on the top right hand side after you login.</td></tr>
            <tr><td>merchant_ref (optional)</td><td>Any value provided by merchant</td><td>This value will be returned with the confirmation results from the confirmation api. VoguePay doesnt need this value, it is used by the merchant to store any data he wishess to retrieve later with the transaction details.</td></tr>
            <tr><td>memo (optional)</td><td>Provided by merchant</td><td>The transaction summary that will show on your transaction history page when you login to VoguePay</td></tr>
            <tr><td>item_x</td><td>Name of product</td><td>The name of the product being purchased. x is a value starting from 1. If there are more than 1 products, you can have item_1, item_2, item_3... as shown in the Sample HTML Form. Each item_x has a corresponding description_x and price_x</td></tr>
            <tr><td>description_x</td><td>Short description of product</td><td>The short description of the product being purchased. x corresponds to the number in item_x.</td></tr>
            <tr><td>price_x</td><td>Price of product.</td><td>The price of the product being purchased. x corresponds to the number in item_x.</td></tr>
            <tr><td>developer_code</td><td>A code unique to every developer. Using  this code earns the developer a commission on every successful transaction made through any selected integration methods.</td><td>This optional field serves as a check for the form. Can be ommited. If included, will be used instead of the sum of all the prices.</td></tr>
            <tr><td>store_id</td><td>A unique store identifier which identifies a particular store a transaction was made.</td></tr>
            <tr><td>total</td><td>Total of all the prices (price_1 + price_2 + price_3...)</td><td>This optional field serves as a check for the form. Can be ommited. If included, will be used instead of the sum of all the prices.</td></tr>
            <tr><td>recurrent</td><td>true</td><td>Allows you to bill a customer repeatedly at a specified interval.</td></tr>
            <tr><td>interval</td><td>Integer</td><td>No of days between each recurrent billing if recurrent is set to true.</td></tr>
            <tr><td>notify_url</td><td>URL</td><td>Url to send payment notification to. If set, this will be used instead of the notification url on your account.</td></tr>
            <tr><td>success_url</td><td>URL</td><td>Url to send buyer back to if payment is successful. If set, this will be used instead of the Success Return URL on your account.</td></tr>
            <tr><td>fail_url</td><td>URL</td><td>Url to send buyer back to if payment is unsuccessful. If set, this will be used instead of the Failure Return URL on your account.</td></tr>
            <tr><td>Submit Image</td><td>http://voguepay.com/images/buttons/buynow_blue.png</td><td>The image to use for submit button. You are free to use any image. We recommend that you use one of the VoguePay buttons from the urls listed below:
    
            https://voguepay.com/images/buttons/buynow_blue.png
    
            https://voguepay.com/images/buttons/buynow_red.png
    
            https://voguepay.com/images/buttons/buynow_green.png
    
            https://voguepay.com/images/buttons/buynow_grey.png
    
            https://voguepay.com/images/buttons/addtocart_blue.png
    
            https://voguepay.com/images/buttons/addtocart_red.png
    
            https://voguepay.com/images/buttons/addtocart_green.png
    
            https://voguepay.com/images/buttons/addtocart_grey.png
    
            https://voguepay.com/images/buttons/checkout_blue.png
    
            https://voguepay.com/images/buttons/checkout_red.png
    
            https://voguepay.com/images/buttons/checkout_green.png
    
            https://voguepay.com/images/buttons/checkout_grey.png
    
            http://voguepay.com/images/buttons/donate_blue.png
    
            http://voguepay.com/images/buttons/donate_red.png
    
            http://voguepay.com/images/buttons/donate_green.png
    
            http://voguepay.com/images/buttons/donate_grey.png
    
            http://voguepay.com/images/buttons/subscribe_blue.png
    
            http://voguepay.com/images/buttons/subscribe_red.png
    
            http://voguepay.com/images/buttons/subscribe_green.png
    
            http://voguepay.com/images/buttons/subscribe_grey.png
    
            http://voguepay.com/images/buttons/make_payment_blue.png
    
            http://voguepay.com/images/buttons/make_payment_red.png
    
            http://voguepay.com/images/buttons/make_payment_green.png
    
            http://voguepay.com/images/buttons/make_payment_grey.png
    
            </td></tr>
		</tbody>
	</table>




# Notification/Order processing API
-------------------------------------

VoguePay sends a transaction id to the notification URL provided in your account for every transaction on that account.

*To recieve a transaction id on your success or failure URL, you must set Send Transaction ID to Success and Failure Return URL to **Yes** on your account preferences page.*


The transaction ID is sent as a HTTP POST variable (transaction_id) e.g:
If your notification URL is **http://mydomain.com/n.php**
then notification will be sent to : 

**http://mydomain.com/n.php**
You can retrieve it as a POST variable e.g ```$_POST['transaction_id']``` for PHP.

You can confirm the status and details of a transaction anytime using our REST(ful) API below:

**https://voguepay.com/**

The api accepts parameters as a GET request. Below is a sample api call.

**https://voguepay.com/?v_transaction_id=11111&type=json**

### Explanation
---------------

<table class="table">
		<tbody><tr><th>Variable</th><th>Acceptable Values</th><th>Default</th><th>Details</th></tr>
		<tr><td>v_transaction_id</td><td>transaction id</td><td></td><td>The transaction id of the transaction to be queried. See sample code below on how to get the transaction id.</td></tr>
		<tr><td>type</td><td>json</td><td>json</td><td>Format for the expected data</td></tr>
		</tbody></table>


Sample JSON Response

```

    {
        "merchant_id":"qa331322179752",
        "transaction_id":"11111",
        "email":"mii@mydomain.com",
        "total":500,
        "total_paid_by_buyer":"507.61",
        "total_credited_to_merchant":"495.00",
        "extra_charges_by_merchant":"0.00",
        "merchant_ref":"2f093e72",
        "memo":"1000 SMS units at &amp;#8358;1.20 each on www.bulksms.com",
        "status":"Approved",
        "date":"2012-01-09 18:56:23",
        "referrer":"http://www.afrisoft.net/viewinvoice.php?id=2012",
        "method":"Interswitch",
        "fund_maturity":"2012-01-11"
    }

```

> For XML Junkies Well I'm Sorry!! I'm Mr. JSON.




### Explanation of responses
----------------------------

<table class="table">
		<tbody>
            <tr><th>Response Key</th><th>Value</th></tr>
            <tr><td>merchant_id</td><td>Merchant ID Of The Seller</td></tr>
            <tr><td>transaction_id</td><td>Transaction ID of the transaction</td></tr>
            <tr><td>email</td><td>email address of buyer</td></tr>
            <tr><td>total</td><td>Total price of products being paid for</td></tr>
            <tr><td>total_paid_by_buyer</td><td>Total amount paid by buyer including any other charges</td></tr>
            <tr><td>total_credited_to_merchant</td><td>Total amount creditable to the merchant's wallet</td></tr>
            <tr><td>extra_charges_by_merchant</td><td>Extra charges placed on buyer by merchant such as taxes e.t.c</td></tr>
            <tr><td>merchant_ref</td><td>merchant_ref value sent with the html form by the merchant</td></tr>
            <tr><td>memo</td><td>Transaction memo that describes the transaction</td></tr>
            <tr><td>status</td><td>Approved or Pending or Failed or Disputed</td></tr>
            <tr><td>date</td><td>Date of transaction in the format <b>yyyy-mm-dd hh:ii:ss</b> e.g 2012-01-09 18:56:23</td></tr>
            <tr><td>referrer</td><td>The merchant page from which the transaction form was sent to VoguePay e.g http://www.afrisoft.net/viewinvoice.php?id=2012</td></tr>
            <tr><td>method</td><td>Method/gateway used for payment e.g Interswitch, voguePay e.t.c</td></tr>
            <tr><td>fund_maturity</td><td>The date that the merchant will be able to withdraw or spend the amount credited to his/her wallet as a result of this transaction</td></tr>
		</tbody>
</table>
		
		
	
			
		
# Test/Demo Accounts
-----------------------

While integrating VoguePay, you may need a test account. We have provided a simple solution to test your integration.

Use demo as your merchant ID in test environment.

Once **"demo"** is used as your merchant ID, you can use any email and password to make payment.

To simulate a Failed transaction, use **failed@anydomain.com** with any password to pay for the transaction e.g: **failed@ivoryserver.com** or *failed@trashmail.com*.

To simulate a successful transaction, use any email and any password to pay for the transaction. You may use your real email since a notification will be sent to the email address you use for the transaction.

The transaction ID will be sent to the notify_url parameter submitted by your form e.g:

```
<input type="hidden" name="notify_url" value="http://www.mydomain.com/notification.php" />
```

You may then call the notification/order processing API from there.


