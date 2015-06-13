@extends('payment.master')

@section('content')

    <div class="row">
        <div class="col-md-offset-8 col-md-3">
            <ol class="breadcrumb">
                <li class="active">Orders</li>
            </ol>
        </div>
    </div>
    {{-- Note You now have $transactionData and $transactionId Within this View--}}
    {{-- Remember to register this route --}}
    {{-- For Readability, You can use Any Other Facade, We Use Pay:: to allow default set gateway to work --}}
    <div class="row">

        <div class="col-md-offset-3 col-md-6">
            <h5 class="text-center">
                We are Assuming You Have Picked Items Into A Cart, Which You will then Retrieve from The Payment Page

            </h5>
            <div class="pad40"></div>
            <hr/>
            <p>
                You are about to Order some lovely Aso Oke's,
                don't worry we have built up the fake order in the controller,
                since we do not want to bundle this with a cart of some sort.

                Read the Controller for more details.
            </p>
            <hr/>


            <a class="pull-right btn btn-lg btn-success" href="{{ route('checkout') }}">
                Proceed to Checkout
            </a>
        </div>

    </div>

@stop