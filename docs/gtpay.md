# GTPay TRANSACTION REQUEST URL

> All transaction requests must be sent to URL:
> **https://ibank.gtbank.com/GTPay/Tranx.aspx**


# SENDING TRANSACTION REQUESTS TO GTPay

Merchants registered on GTPay send transaction requests to GTPay via HTTP Post to URL https://ibank.gtbank.com/GTPay/Tranx.aspx . When merchant's customer on merchant's site concludes selection of items to purchase and chooses to pay via GTPay, the merchant's site will send such transaction request to GTPay via HTTP Post including the HTTP parameters described in the table below:


<table width="100%" cellpadding="5" cellspacing="0" border="1">
    <tbody>
        <tr style="font-weight:bold; background-color: #cccccc">
            <td width="20%">Parameter</td><td width="55%">Description</td><td width="15%">Type</td><td width="10%">Max Length</td>
        </tr>
        <tr>
            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        </tr>
        <tr>
            <td>gtpay_mert_id</td>
            <td>
                <b>Required</b><br><br>
                This is the GTPay-wide unique identifier of merchant, assigned by GTPay and communicated to merchant by GTBank.
            </td>
            <td>numeric</td>
            <td>4</td>
        </tr>
        <tr>
            <td>gtpay_tranx_id</td>
            <td>
                <b>Required</b><br><br>
                This is a merchant-wide unique identifier of each transaction request sent to GTPay by merchant. The transaction ID must be unique per transaction. The minimum lenght is 6 and the maximum lenght is 60

            </td>
            <td>--</td>
            <td>30</td>
        </tr>
        <tr>
            <td>gtpay_tranx_amt</td>
            <td>
                <b>Required</b><br><br>
                The total monetary value of transaction. This value must be in kobo (Naira) or cents(Dollars). E.g N5 should be sent as 500

            </td>
            <td>numeric</td>
            <td>10</td>
        </tr>

        <tr>
            <td>gtpay_tranx_curr</td>
            <td>
                <b>Optional</b><br><br>
                This parameter represents the ISO currency code representing the currency in which the transaction is been carried out. If not
                specified, merchant's currency symbol in GTPay's database will be used. Acceptable values Naira(566). USD(844)
            </td>
            <td>--</td>
            <td>3</td>
        </tr>
        <tr>
            <td>gtpay_cust_id</td>
            <td>
                <b>Required</b><br><br>
                The value of this parameter will be the merchant-wide unique identifier of the customer. For example, for a student
                paying for school fees online, this may be the student's School's Registration Number, since the registration number
                is unique to that student throughout the whole school.
            </td>
            <td>--</td>
            <td>30</td>
        </tr>
        <tr>
            <td>gtpay_tranx_memo</td>
            <td>
                <b>Optional</b><br><br>
                This describes the transaction to the customer. For example, gtpay_tranx_memo = "John Adebisi (REG13762) : 2nd Term
                School Fees Payment"
                <p>If not sent, "Purchasing from [Business-Name-Of-Merchant]" will be used</p>
            </td>
            <td>--</td>
            <td>120</td>
        </tr>
        <tr>
            <td>gtpay_tranx_noti_url</td>
            <td>
                <b>Required</b><br><br>
                The URL to which GTPay should send transaction status report to on completion of
                transaction.
            </td>
            <td>URL characters</td>
            <td>120</td>
        </tr>



        <tr>
            <td>gtpay_gway_name</td>
            <td>
                <b>Optional</b><br><br>
                If specified, then customer cannot choose what gateway to use for the transaction. Accepted values
                are "webpay" or "migs" (Mastercard International Gateway) only.
            </td>
            <td>webpay or migs (Mastercard International Gateway)</td>
            <td>9</td>
        </tr>
        <tr>
            <td>gtpay_gway_first</td>
            <td>
                <b>Optional</b><br><br>
                If GTPay should take customer directly from merchant's page to the gateway to effect the debit/credit.
                If not specified, customer will be shown GTPay's own first page, from where he/she will click Continue to go
                to the gateway.
                <p>NOTE: If specified, then gtpay_gway_name must be specified.</p>
            </td>
            <td>yes or no</td>
            <td>3</td>
        </tr>
        <tr>
            <td>gtpay_echo_data</td>
            <td>
                <b>Optional</b><br><br>
                Merchant can store in this parameter any data that it needs returned back at transaction completion.
            </td>
            <td>--</td>
            <td>255</td>
        </tr>
          <tr>
            <td>gtpay_cust_name</td>
            <td>
                <b>Optional</b><br><br>
                Merchant can store in this the name to be displayed on the payment page for the customer.
            </td>
            <td>--</td>
            <td>30</td>
        </tr>
         <tr>
            <td>gtpay_tranx_hash</td>
            <td>
                <b>Required</b><br><br>
                New Merchant are required to perform a sha512 hash of [gtpay_tranx_id + gtpay_tranx_amt + gtpay_tranx_noti_url + hashkey] (in
                that order. Please note that the hash key will be provided on setup)
            </td>
            <td>--</td>
            <td>variable</td>
        </tr>
    </tbody>
</table>


Following is an example transaction request (you may copy and paste the HTML code into a text file, save it as HTML file and open the file):

```

    <form name="submit2gtpay_form" action="https://ibank.gtbank.com/GTPay/Tranx.aspx" target="_self" method="post">
        <input type="hidden" name="gtpay_mert_id" value="17" />
        <input type="hidden" name="gtpay_tranx_id" value="" />
        <input type="hidden" name="gtpay_tranx_amt" value="5000" />
        <input type="hidden" name="gtpay_tranx_curr" value="566" />
        <input type="hidden" name="gtpay_cust_id" value="458742" />
        <input type="hidden" name="gtpay_cust_name" value="Test Customer" />
        <input type="hidden" name="gtpay_tranx_memo" value="Mobow" />
        <input type="hidden" name="gtpay_no_show_gtbank" value="yes" />
        <input type="hidden" name="gtpay_echo_data" value="TEST" />
        <input type="hidden" name="gtpay_gway_name" value="" />
        <input type="hidden" name="gtpay_tranx_hash" value="" />
        <input type="hidden" name="gtpay_tranx_noti_url" value="" />
        <input type="submit" value="Pay Via GTPay" name="btnSubmit"/>
        <input type="hidden" name="gtpay_echo_data" value="DEQFOOIPP0;REG13762;John Adebisi: 2nd term school and accomodation fees;XNFYGHT325541;1209">
    </form>
```

There is a test merchant "GT-Merchant" configured on GTPay, you can test this at URL:
**https://ibank.gtbank.com/GTPay/test/Test.html**


The transaction processing steps are now described in details as follows:

#### GTPay receives transaction request and validates it
    On receiving transaction request from merchant, GTPay validates the request, making sure that required parameters
    are supplied with appropriate values, transaction amount can be correctly determined, etc. If transaction request
    fails this validation phase, the transaction is NOT logged in GTPay's database, is rejected on the basis of invalid
    transaction, and a status code of -2 is returned to merchant, or shown to customer in the case gtpay_tranx_noti_url
    is not supplied, denoting INVALID TRANSACTION, and GTPay simply terminates the transaction.

#### Transaction request passes GTPay validation and is forwarded to gateway
    If the transaction request passes GTPay's validation, GTPay logs all information relating to the transaction request,
    then, if gtpay_gway_first is not specified or its value is "no", shows the customer its own first page, on which customer
    can choose the gateway to use for transaction if gtpay_gway_name is not specified, and the customer clicks the "Continue"
    button to go to the gateway for debit/credit. If, however, gtpay_gway_first is yes, then the customer is simply taken
    straight to the gateway specified by gtpay_gway_name.

#### Gateway attempts transaction debit/credit leg and reports back to GTPay
    After logging details of transaction request in its database, GTPay forwards the debit/credit leg of the transaction request
    to the merchant-specified or customer-chosen gateway which handles the debit/credit and reports the result to GTPay.

#### GTPay receives transaction status from gateway and completes transaction
    On receiving the transaction status report from the gateway, GTPay updates the transaction information in its database
    with the received status report. Then sends the status report to the merchant or shows it to customer in case gtpay_tranx_noti_url
    is not known. The transaction ends from GTPay's point of view.



# GTPay RETURNED TRANSACTION STATUS REPORT PARAMETERS

On transactions completion, if merchant desires status of transactions, GTPay will send the following parameters to describe the transaction and how it has fared:


<table width="100%" cellpadding="5" cellspacing="0" border="1">
    <tbody>
        <tr style="font-weight:bold; background-color: #cccccc">
            <td width="20%">Parameter</td><td width="55%">Description</td>
        </tr>
        <tr>
            <td>&nbsp;</td><td>&nbsp;</td>
        </tr>
        <tr>
            <td>gtpay_tranx_id</td>
            <td>
                <b></b><br><br>
                The merchant-wide unique transaction identifier, sent by merchant in the transaction request sent, or
                generated for merchant by GTPay
            </td>
        </tr>
        <tr>
            <td>gtpay_tranx_status_code</td>
            <td>
                <b></b><br><br>
                The transaction status code denoting how transaction fared
            </td>
        </tr>
        <tr>
            <td>gtpay_tranx_status_msg</td>
            <td>
                <b></b><br><br>
                A description of gtpay_tranx_status_code
            </td>
        </tr>
        <tr>
            <td>gtpay_tranx_amt</td>
            <td>
                <b></b><br><br>
                The total monetary value of transaction
            </td>
        </tr>
        <tr>
            <td>gtpay_tranx_curr</td>
            <td>
                <b></b><br><br>
                The code used to denote the currency in which transaction was carried out (566 or 844)
            </td>
        </tr>
        <tr>
            <td>gtpay_cust_id</td>
            <td>
                <b></b><br><br>
                The merchant-wide unique identifier of the customer that carried out transaction
            </td>
        </tr>


        <tr>
            <td>gtpay_gway_name</td>
            <td>
                <b></b><br><br>
                The name of the gateway that serviced transaction. This will be either webpay or etranzact
            </td>
        </tr>
                                               <tr>
            <td>gtpay_echo_data</td>
            <td>
                <b></b><br><br>
                The miscellaneous data merchant sent earlier and wants returned at transaction completion
            </td>
        </tr>
         <tr>
            <td>Note on gtpay_tranx_status_code</td>
            <td>
                <b></b><br><br>
                For ALL transaction gtpay_tranx_status_code of '00' denotes succesfull transaction ANY other response code is a failed transaction
            </td>
        </tr>
    </tbody>
</table>


# GTPay TRANSACTION ERROR CODES

<table width="100%" cellpadding="5" cellspacing="0" border="1">

     <tbody>
          <tr>
                <td><b>G000</b></td>
                <td>
                    <b>Transaction Error</b><br><br>
                    Error occured during transaction initiation. Please retry
                </td>
                <td>Retry the transaction by sending a new request.</td>
            </tr>
               <tr>
                <td><b>G100</b></td>
                <td>
                    <b>Invalid merchant</b><br><br>
                    Specified merchant ID is not known to GTPay, or the ID is not specified
                </td>
                <td>Be sure to be sending the ID communicated to you as your GTPay-Assigned merchant ID</td>
            </tr>
            <tr>
                <td><b>G101</b></td>
                <td>
                    <b>Invalid customer id</b><br><br>
                    Merchant's request to GTPay does not include required request parameter gtpay_cust_id, or the parameter's
                    value is empty
                </td>
                <td>Include the parameter with a non-empty value</td>
            </tr>
            <tr>
                <td><b>G102</b></td>
                <td>
                    <b>Missing transaction amount</b><br><br>
                    GTPay cannot for some reason determine the total monetary value of transaction request, or the value is not
                    numeric or is 0 or less
                    or contains comma (,) or a decimal point.</td>
                <td>Include the parameter gtpay_tranx_amt value stated in kobo(Naira) or cents
                    (Dollar)</td>
            </tr>
              <tr>
                <td><b>G103</b></td>
                <td>
                    <b>Invalid transaction amount</b><br><br>
                    GTPay cannot for some reason determine the total monetary value of transaction request, or the value is not
                    numeric or is 0 or less
                    or contains comma (,) or a decimal point.</td>
                <td>Include the parameter gtpay_tranx_amt value stated in kobo(Naira) or cents
                    (Dollar)</td>
            </tr>
              <tr>
                <td><b>G104</b></td>
                <td>
                    <b>Duplicate transaction id</b><br><br>
                    Merchant sent a transaction id which has already been sent for some previous transaction by same merchant.
                </td>
                <td>Resend transaction with a new unique transaction ID</td>
            </tr>
             <tr>
                <td><b>G105</b></td>
                <td>
                    <b>Invalid transaction id</b><br><br>
                    Request parameter gtpay_tranx_id is present in request but its value is empty
                </td>
                <td>Resend transaction with a non-empty unique transaction ID</td>
            </tr>

            <tr>
                <td class="style1"><b>G106</b></td>
                <td class="style1">
                    <b>Invalid transaction notification url</b><br><br>
                    Request parameter gtpay_tranx_noti_url is specified but its value is not in valid URL format
                </td>
                <td class="style1">Edit the value into a valid URL</td>
            </tr>
             <tr>
                <td class="style1"><b>G107</b></td>
                <td class="style1">
                    <b>Missing Transaction hash</b><br><br>
                   The hash value of the parameters was not provided as part of the POST parameter
                </td>
                <td class="style1">Add the transaction hash and try again.</td>
            </tr>
            <tr>
                <td><b>G108</b></td>
                <td>
                    <b>Interface Integration Error</b><br><br>
                     The hash value of the parameters do not match
                </td>
                <td>Check the way the  hash value is being generated and retry </td>
            </tr>

          <tr>
                <td><b>G109</b></td>
                <td>
                    <b>Invalid gateway</b><br><br>
                    Request&nbsp; gtpay_gway_name is not specified, or its value is empty, or its value is
                    not webpay,ibank or migs.
                </td>
                <td>Edit the value of gtpay_gway_name as webpay or etranzact or migs</td>
            </tr>
     </tbody>
</table>



# GTPay Transaction Requery:

On transactions completion, it is imperative for the merchant to requery the status of the transaction to verify the transaction status at gateway. This is to eliminate man in the middle attack.

<table width="100%" cellpadding="5" cellspacing="0" border="1">
    <tbody>
        <tr style="font-weight:bold; background-color: #cccccc">
            <td class="style2">Parameter( Case Sensitive)</td><td width="55%">Description</td>
        </tr>
        <tr>
            <td class="style2">&nbsp;</td><td>&nbsp;</td>
        </tr>
        <tr>
            <td class="style2">mertid</td>
            <td>
                <b></b><br><br>
                This is the GTPay assaigned merchant id
            </td>
        </tr>
        <tr>
            <td class="style2">amount</td>
            <td>
                <b></b><br><br>
                Transaction amount in kobo
            </td>
        </tr>
        <tr>
            <td class="style2">tranxid</td>
            <td>
                <b></b><br><br>
                The Transaction ID
            </td>
        </tr>
        <tr>
            <td class="style2">hash </td>
            <td>
                <b></b><br><br>
                sha512 hash of the following : mertid + tranxid + hashkey
            </td>
        </tr>
        <tr>
            <td class="style2">hashkey</td>
            <td>
                <b></b><br><br>
                hashkey will be provided on setup. Please keep the value secure.
            </td>
        </tr>
        <tr>
            <td class="style2">Sample REST Request</td>
            <td>
                <b></b><br><br>
                https://ibank.gtbank.com/GTPayService/gettransactionstatus.xml?mertid=212&amp;amount=200000&amp;tranxid=PLM_1394115494_11180&amp;hash=F48289B1C72218C6C02884C26438FA070864B624D1FD82C90F858AF268B2B82F7A3D2311400B29E9B3731068B89EB8007F36B642838C821CAB47D2AAFB5FA0EF
            </td>
        </tr>


        <tr>
            <td class="style2">Sample json Request</td>
            <td>
                <b></b><br><br>
               https://ibank.gtbank.com/GTPayService/gettransactionstatus.json?mertid=212&amp;amount=200000&amp;tranxid=PLM_1394115494_11180&amp;hash=F48289B1C72218C6C02884C26438FA070864B624D1FD82C90F858AF268B2B82F7A3D2311400B29E9B3731068B89EB8007F36B642838C821CAB47D2AAFB5FA0EF                                            </td>
        </tr>
        <tr>
            <td class="style2">Sample json Response</td>
            <td>
                <b></b><br><br>
                {"Amount":"2600","MerchantReference":"FBN|WEB|UKV|19-12-2013|037312","MertID":"17","ResponseCode":"00","ResponseDescription":"Approved by Financial Institution"}
            </td>
        </tr>
    </tbody>
</table>





|

 <a name="top">

## <span style="color:#777777; font-size:30px">Merchant Manual (Technical)</span>

</a> |

[GTPay TRANSACTION REQUEST URL](#url)

*   [SENDING TRANSACTION REQUESTS TO GTPay](#request)

*   [GTPay TRANSACTION PROCESSING](#process)

*   [GTPay RETURNED TRANSACTION STATUS REPORT PARAMETERS](#returned)

*   [GTPay TRANSACTION ERROR CODES](#errors)

*   [GTPay TRANSACTION STATUS CODES AND DESCRIPTIONS](#codes)

*   [GT-Merchant (GTPay test merchant)](#test)

 |
 <a name="integrate"></a> |
 <a name="url">

### GTPay TRANSACTION REQUEST URL

All transaction requests must be sent to URL:

https://ibank.gtbank.com/GTPay/Tranx.aspx

</a> |
 <a name="request">

### SENDING TRANSACTION REQUESTS TO GTPay

Merchants registered on GTPay send transaction requests to GTPay via HTTP Post to URL https://ibank.gtbank.com/GTPay/Tranx.aspx . When merchant's customer on merchant's site concludes selection of items to purchase and chooses to pay via GTPay, the merchant's site will send such transaction request to GTPay via HTTP Post including the HTTP parameters described in the table below:

| Parameter | Description | Type | Max Length |
| gtpay_mert_id | **Required**

 This is the GTPay-wide unique identifier of merchant, assigned by GTPay and communicated to merchant by GTBank. | numeric | 4 |
| gtpay_tranx_id | **Required**

 This is a merchant-wide unique identifier of each transaction request sent to GTPay by merchant. The transaction ID must be unique per transaction. The minimum lenght is 6 and the maximum lenght is 60 | -- | 30 |
| gtpay_tranx_amt | **Required**

 The total monetary value of transaction. This value must be in kobo (Naira) or cents(Dollars). E.g N5 should be sent as 500 | numeric | 10 |
| gtpay_tranx_curr | **Required**

 This parameter represents the ISO currency code representing the currency in which the transaction is been carried out. Acceptable values Naira  - (566). USD  - (826) | -- | 3 |
| gtpay_cust_id | **Required**

 The value of this parameter will be the merchant-wide unique identifier of the customer. For example, for a student paying for school fees online, this may be the student's School's Registration Number, since the registration number is unique to that student throughout the whole school. | -- | 30 |
| gtpay_tranx_memo | **Optional**

 This describes the transaction to the customer. For example, gtpay_tranx_memo = "John Adebisi (REG13762) : 2nd Term School Fees Payment"

If not sent, "Purchasing from [Business-Name-Of-Merchant]" will be used

 | -- | 120 |
| gtpay_tranx_noti_url | **Required**

 The URL to which GTPay should send transaction status report to on completion of transaction. | URL characters | 120 |
| gtpay_gway_name | **Optional**

 If specified, then customer cannot choose what gateway to use for the transaction. Accepted values are "webpay" or "migs" (Mastercard International Gateway) only. | webpay or migs (Mastercard International Gateway) | 9 |
| gtpay_gway_first | **Optional**

 If GTPay should take customer directly from merchant's page to the gateway to effect the debit/credit. If not specified, customer will be shown GTPay's own first page, from where he/she will click Continue to go to the gateway.

NOTE: If specified, then gtpay_gway_name must be specified.

 | yes or no | 3 |
| gtpay_echo_data | **Optional**

 Merchant can store in this parameter any data that it needs returned back at transaction completion. | -- | 255 |
| gtpay_cust_name | **Optional**

 Merchant can store in this the name to be displayed on the payment page for the customer. | -- | 30 |
| gtpay_hash | **Required**

 Merchants are required to perform a sha512 hash of [gtpay_mert_id,gtpay_tranx_id,gtpay_tranx_amt,gtpay_tranx_curr,gtpay_cust_id,gtpay_tranx_noti_url] (in that order. Please note that the hash key will be provided on setup) | -- | variable |

Following is an example transaction request (you may copy and paste the HTML code into a text file, save it as HTML file and open the file):

</a>

<pre> <a name="request"><html>
<body onload="document.submit2gtpay_form.submit()">
<form name="submit2gtpay_form" action="https://ibank.gtbank.com/GTPay/Tranx.aspx" target="_self"</a> <a name="request0">method="post"</a>>
<input type="hidden" name="gtpay_mert_id" value="17" />
<input type="hidden" name="gtpay_tranx_id" value="" />
<input type="hidden" name="gtpay_tranx_amt" value="5000" />
<input type="hidden" name="gtpay_tranx_curr" value="566" />
<input type="hidden" name="gtpay_cust_id" value="458742" />
<input type="hidden" name="gtpay_cust_name" value="Test Customer" />
<input type="hidden" name="gtpay_tranx_memo" value="Mobow" />
<input type="hidden" name="gtpay_no_show_gtbank" value="yes" />
<input type="hidden" name="gtpay_echo_data" value="TEST" />
<input type="hidden" name="gtpay_gway_name" value="" />
<input type="hidden" name="gtpay_tranx_hash" value="" />
<input type="hidden" name="gtpay_tranx_noti_url" value="" />
<input type="submit" value="Pay Via GTPay" name="btnSubmit"/>
<input type="hidden" name="gtpay_echo_data" value="DEQFOOIPP0;REG13762;John Adebisi: 2nd term school and accomodation fees;XNFYGHT325541;1209">
</form>
</body>
</html>
                                        </pre>

There is a test merchant "GT-Merchant" configured on GTPay, you can test this at URL:

[https://ibank.gtbank.com/GTPay/test/Test.html](https://ibank.gtbank.com/GTPay/test/Test.html)

 [Back To Top](#top)
  |
 <a name="process">

The transaction processing steps are now described in details as follows:

</a>

<a name="process"></a>
*   <a name="process">**GTPay receives transaction request and validates it**</a>

    > <a name="process">On receiving transaction request from merchant, GTPay validates the request, making sure that required parameters are supplied with appropriate values, transaction amount can be correctly determined, etc. If transaction request fails this validation phase, the transaction is **NOT** logged in GTPay's database, is rejected on the basis of invalid transaction, and a status code of -2 is returned to merchant, or shown to customer in the case gtpay_tranx_noti_url is not supplied, denoting INVALID TRANSACTION, and GTPay simply terminates the transaction.</a>

*   **Transaction request passes GTPay validation and is forwarded to gateway**

    > If the transaction request passes GTPay's validation, GTPay logs all information relating to the transaction request, then, if gtpay_gway_first is not specified or its value is "no", shows the customer its own first page, on which customer can choose the gateway to use for transaction if gtpay_gway_name is not specified, and the customer clicks the "Continue" button to go to the gateway for debit/credit. If, however, gtpay_gway_first is yes, then the customer is simply taken straight to the gateway specified by gtpay_gway_name.

*   **Gateway attempts transaction debit/credit leg and reports back to GTPay**

    > After logging details of transaction request in its database, GTPay forwards the debit/credit leg of the transaction request to the merchant-specified or customer-chosen gateway which handles the debit/credit and reports the result to GTPay.

*   **GTPay receives transaction status from gateway and completes transaction**

    > On receiving the transaction status report from the gateway, GTPay updates the transaction information in its database with the received status report. Then sends the status report to the merchant or shows it to customer in case gtpay_tranx_noti_url is not known. The transaction ends from GTPay's point of view.

 [Back To Top](#top)
  |
 <a name="returned">

### GTPay RETURNED TRANSACTION STATUS REPORT PARAMETERS

On transactions completion, if merchant desires status of transactions, GTPay will send the following parameters to describe the transaction and how it has fared:

| Parameter | Description |
| gtpay_tranx_id |

The merchant-wide unique transaction identifier, sent by merchant in the transaction request sent, or generated for merchant by GTPay |
| gtpay_tranx_status_code |

The transaction status code denoting how transaction fared |
| gtpay_tranx_status_msg |

A description of gtpay_tranx_status_code |
| gtpay_tranx_amt |

The total monetary value of transaction in Naira or Dollars |
| gtpay_tranx_curr |

The code used to denote the currency in which transaction was carried out (566 or 844) |
| gtpay_cust_id |

The merchant-wide unique identifier of the customer that carried out transaction |
| gtpay_gway_name |

The name of the gateway that serviced transaction. This will be either webpay or etranzact |
| gtpay_echo_data |

The miscellaneous data merchant sent earlier and wants returned at transaction completion |
| gtpay_tranx_amt_small_denom | This is the transaction amount in (kobo or cents) units. |
| gtpay_verification_hash | This is the sha 512 hash of the following parameters, gtpay_tranx_id , gtpay_tranx_amt_small_denom , gway_status_code , hash key. This can be used as a first level verification of the transaction status code. This should be done by the website to prevent man in the middle attack |
| Note on gtpay_tranx_status_code |

For ALL transaction gtpay_tranx_status_code of '00' denotes succesfull transaction ANY other response code is a failed transaction |

 </a>[Back To Top](#top)
  |
 <a name="errors">

### GTPay TRANSACTION ERROR CODES

| **G000** | **Transaction Error**

 Error occured during transaction initiation. Please retry | Retry the transaction by sending a new request. |
| **G100** | **Invalid merchant**

 Specified merchant ID is not known to GTPay, or the ID is not specified | Be sure to be sending the ID communicated to you as your GTPay-Assigned merchant ID |
| **G101** | **Invalid customer id**

 Merchant's request to GTPay does not include required request parameter gtpay_cust_id, or the parameter's value is empty | Include the parameter with a non-empty value |
| **G102** | **Missing transaction amount**

 GTPay cannot for some reason determine the total monetary value of transaction request, or the value is not numeric or is 0 or less or contains comma (,) or a decimal point. | Include the parameter gtpay_tranx_amt value stated in kobo(Naira) or cents (Dollar) |
| **G103** | **Invalid transaction amount**

 GTPay cannot for some reason determine the total monetary value of transaction request, or the value is not numeric or is 0 or less or contains comma (,) or a decimal point. | Include the parameter gtpay_tranx_amt value stated in kobo(Naira) or cents (Dollar) |
| **G104** | **Duplicate transaction id**

 Merchant sent a transaction id which has already been sent for some previous transaction by same merchant. | Resend transaction with a new unique transaction ID |
| **G105** | **Invalid transaction id**

 Request parameter gtpay_tranx_id is present in request but its value is empty | Resend transaction with a non-empty unique transaction ID |
| **G106** | **Invalid transaction notification url**

 Request parameter gtpay_tranx_noti_url is specified but its value is not in valid URL format | Edit the value into a valid URL |
| **G107** | **Missing Transaction hash**

 The hash value of the parameters was not provided as part of the POST parameter | Add the transaction hash and try again. |
| **G108** | **Interface Integration Error**

 The hash value of the parameters do not match | Check the way the hash value is being generated and retry |
| **G109** | **Invalid gateway**

 Request  gtpay_gway_name is not specified, or its value is empty, or its value is not webpay,ibank or migs. | Edit the value of gtpay_gway_name as webpay or etranzact or migs |

 </a>[Back To Top](#top)
  |
 <a name="returned">

### GTPay Transaction Requery:

On transactions completion, it is imperative for the merchant to requery the status of the transaction to verify the transaction status at gateway. This is to eliminate man in the middle attack.

| Parameter( Case Sensitive) | Description |
| mertid |

This is the GTPay assaigned merchant id |
| amount |

Transaction amount in kobo |
| tranxid |

The Transaction ID |
| hash |

sha512 hash of the following : mertid + tranxid + hashkey |
| hashkey |

hashkey will be provided on setup. Please keep the value secure. |
| Sample REST Request |

https://ibank.gtbank.com/GTPayService/gettransactionstatus.xml?mertid=212&amount=200000&tranxid=PLM_1394115494_11180&hash=F48289B1C72218C6C02884C26438FA070864B624D1FD82C90F858AF268B2B82F7A3D2311400B29E9B3731068B89EB8007F36B642838C821CAB47D2AAFB5FA0EF |
| Sample json Request |

https://ibank.gtbank.com/GTPayService/gettransactionstatus.json?mertid=212&amount=200000&tranxid=PLM_1394115494_11180&hash=F48289B1C72218C6C02884C26438FA070864B624D1FD82C90F858AF268B2B82F7A3D2311400B29E9B3731068B89EB8007F36B642838C821CAB47D2AAFB5FA0EF |
| Sample json Response |

{"Amount":"200000","MerchantReference":"ZIB|WEB|IthenaL|6-03-2014|011592","MertID":"212","ResponseCode":"00","ResponseDescription":"Approved by Financial Institution","TransactionCurrency":"566"} |

 </a>[Back To Top](#top)
  |
 <a name="test">

### GT-Merchant (GTPay test merchant)

</a>

<a name="test">To test, browse:</a> [Test Page](https://ibank.gtbank.com/gtpay/test/testmerchant.aspx)

 [Back To Top](#top)
  |

 |
