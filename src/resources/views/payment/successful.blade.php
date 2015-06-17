@extends('payment.master')

@section('content')

    <div class="row">
        <div class="col-md-offset-8 col-md-3">
            <ol class="breadcrumb">
                <li class="active">Transaction Successful</li>
            </ol>
        </div>
    </div>
    {{-- Note You now have $transactionData and $transactionId Within this View--}}
    {{-- Remember to register this route --}}
    {{-- For Readability, You can use Any Other Facade, We Use Pay:: to allow default set gateway to work --}}
    <div class="row">

        <div class="col-md-offset-3 col-md-6">
            <h5 class="text-center">
                <i class="fa fa-check fa-2x text-success"></i> Transaction Successful

            </h5>
            <div class="pad40"></div>

            <p><b>Transaction Reference:</b> {!! $result['transaction_id'] !!}</p>
            <p><b>Merchant Reference:</b> {!! $result['merchant_ref'] !!}</p>
            <p><b>Transaction Status:</b> {!! $result['status'] !!}</p>
            <p><b>Amount Paid:</b> {!! $result['amount'] !!}</p>
            <p><b>Paid by:</b> {!! $result['customer_id'] !!}</p>

            <hr/>
            <p>
                Thank you for your patronage, You are a the best!
                by the way we've shot an invoice of this transaction into your inbox.
            </p>


            <hr/>


            <a class="pull-right btn btn-lg btn-success" href="{{ route('orders') }}">
                Continue
            </a>
        </div>

    </div>

@stop
