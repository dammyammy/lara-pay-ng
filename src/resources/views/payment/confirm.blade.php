@extends('payment.master')

@section('content')

    <div class="row">
        <div class="col-md-offset-8 col-md-3">
            <ol class="breadcrumb">
                <li><a href="#">Orders</a></li>
                <li class="active">Checkout Confirmation</li>
            </ol>
        </div>
    </div>
    {{-- Note You now have $transactionData and $transactionId Within this View--}}
    {{-- Remember to register this route --}}
    <div class="row">

        <div class="col-md-offset-3 col-md-6">
            @if(isset($merchantRef) && isset($transactionData) && isset($items))
                <h5 class="text-center pad40">Your Reference No.: {!! $merchantRef !!}</h5>
                <div class="table-responsive">
                    <div class="pad40"></div>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <td class="active">Items</td>
                            <td class="success">Price</td>
                            <td class="warning">Description</td>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($items as $key => $value)
                            <tr>

                                <th  scope="row" class="">
                                    <pre>{!! ($value['item']) !!}</pre>
                                </th>

                                <th  scope="row" class="">
                                    <pre>{!! ($value['price']) !!}</pre>
                                </th>
                                @if(isset($value['description']))
                                <th  scope="row" class="">
                                    <pre>{!! ($value['description']) !!}</pre>
                                </th>
                                @else
                                <th scope="row"><pre>N/A</pre></th>
                                @endif

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                </div>
                <div class="pull-right">
                    {{-- For Readability, You can use Any Other Facade, We Use Pay:: to allow default set gateway to work --}}
                    {!! Pay::payButton($merchantRef, $transactionData) !!}
                </div>
            @else

                <h1>You Cannot See a button as This Page Was Gotten to outside the normal Flow</h1>
            @endif
        </div>

    </div>

@stop