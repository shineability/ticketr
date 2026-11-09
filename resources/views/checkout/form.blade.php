@extends('layout.app')

@section('title', 'Checkout')

@section('content')
    <h2>{{ $ticket->title }}</h2>
    <h5 class="text-muted">{{ $ticket->organizer->name }}</h5>
    <form method="post" action="{{ route('checkout', ['ticket' => $ticket]) }}">
        @csrf
        <div class="form-group my-4">
            <label for="quantity">Quantity</label>
            <select name="quantity" id="quantity" class="form-control form-control-lg @error('quantity') is-invalid @enderror">
                @for ($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" @selected(old('quantity', 1) == $i)>{{ $i }}</option>
                @endfor
            </select>
            @error('quantity')
                <small class="invalid-feedback"><strong>{{ $message }}</strong></small>
            @enderror
        </div>
        <div class="form-group my-4">
            <label for="email" class="is-invalid">Email</label>
            <input name="email" value="{{ old('email') }}" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" required>
            @error('email')
                <small class="invalid-feedback"><strong>{{ $message }}</strong></small>
            @else
                <small class="form-text text-muted">The QR code for this event will be sent to this email address.</small>
            @enderror
        </div>
        <button type="submit" class="btn btn-lg btn-primary">Buy for <strong>{{ $ticket->formatted_price }}</strong> each</button>
        <a class="btn btn-link text-muted" href="{{ route('home') }}" role="button">Back to tickets</a>
    </form>
@endsection
