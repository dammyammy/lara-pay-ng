

{{-- Note You now have $transactionData and $transactionId Within this View--}}
{{-- Remember to register this route --}}
{{-- For Readability, You can use Any Other Facade, We Use Pay:: to allow default set gateway to work --}}
@if(isset($transactionData) && isset($transactionId))

    {{ Pay::payButton($transactionId, $transactionData) }}

@endif