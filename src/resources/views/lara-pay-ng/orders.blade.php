@extends('vendor.lara-pay-ng.master')

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
            <h4 class="text-center">Choose Your Poison</h4>
            <hr/>
            <div class="well col-md-5">
                <h5><b>Pay for Subscription</b></h5>
                <p>
                    You are about to Order a Membership subscription for music club. Billed every 30 days.
                    It is a recurrent bill.


                    Read the Controller for more details.
                </p>
                <a class="btn btn-lg btn-success" href="{{ route('checkout') . '?type=subscription' }}">
                    Proceed to Checkout
                </a>

            </div>
            <div class="well col-md-offset-1 col-md-6">
                <h5><b>Pay for Products</b></h5>
                <p>
                    You are about to Order some lovely Aso Oke's,
                    don't worry we have built up the fake order in the controller,
                    since we do not want to bundle this with a cart of some sort.

                    Read the Controller for more details.
                </p>
                <a class="btn btn-lg btn-success" href="{{ route('checkout') . '?type=products'  }}">
                    Proceed to Checkout
                </a>
            </div>

            <hr/>



        </div>

    </div>

@stop
