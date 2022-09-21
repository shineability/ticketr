@extends('layout.app')

@section('title', 'Select your ticket')

@section('content')

    @if (session('checkout.order'))
        @if (session('checkout.order')->isCompleted())
            <div class="alert alert-success mb-5 p-4" role="alert">
                <h4 class="alert-heading">Thank you for your order!</h4>
                <p class="mb-0">An email with the QR code for this event has been sent to <strong>{{ session('checkout.order')->email }}</strong>.</p>
            </div>
        @elseif (session('checkout.order')->isCanceled())
            <div class="alert alert-warning mb-4 p-3" role="alert">
                Your order has been canceled...
            </div>
        @endif
    @endif

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4">
    @foreach ($tickets as $ticket)
        <div class="col mb-4">
            <div class="card flex-fill">
                <img src="{{ $ticket->image }}" class="card-img-top" style="height: 10rem; object-fit: cover; object-position: 50% 0%;"  alt="{{ $ticket->title }}"/>
                <div class="card-body">
                    <h5 class="card-title">{{ $ticket->title }}</h5>
                    <h6 class="card-subtitle mb-3 text-muted text-black-50">{{ $ticket->organizer->name }}</h6>
                    <span class="float-left mt-1">{{ $ticket->formatted_price }}</span>
                    <a href="{{ route('checkout.form', $ticket) }}" class="stretched-link btn btn-sm btn-primary float-right"><strong>Buy now</strong></a>
                </div>
            </div>
        </div>
    @endforeach
    </div>

@endsection
