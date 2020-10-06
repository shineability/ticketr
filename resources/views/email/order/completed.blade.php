@component('mail::message')
<h1 style="text-align: center;">Order confirmation #{{ $order->reference }}</h1><br>
<p style="text-align: center; font-size: 2em"><strong>{{ $order->ticket->title }}</strong></p>
<center><img src="{{ $order->qr_code_url }}"></center>
@endcomponent
