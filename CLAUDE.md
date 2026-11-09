# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Ticketr is a multi-tenant ticketing platform built with Laravel 12 where organizers can sell tickets using different payment providers (Mollie, Stripe). Each organizer can link one payment provider account to process payments.

## Development Commands

```bash
# Start development environment
./vendor/bin/sail up

# Run database setup (migrations + seed)
./vendor/bin/sail composer setup

# Run tests
./vendor/bin/sail test

# Run a single test file
./vendor/bin/sail test tests/Feature/CheckoutTest.php

# Run tests with a filter
./vendor/bin/sail test --filter=CheckoutTest

# Format code
./vendor/bin/sail composer pint
```

## Architecture

### Payment Provider System (Strategy Pattern)

The application uses the Strategy pattern to support multiple payment providers interchangeably:

- **Contracts**: `App\Payment\Contracts\PaymentProvider` and `App\Payment\Contracts\Payment` define the interfaces
- **Factory**: `App\Payment\PaymentProviderFactory` creates provider instances, accessible via `PaymentProvider` facade
- **Implementations**: Located in `app/Payment/{Mollie,Stripe,Free}/`

Each provider implements:
```php
interface PaymentProvider {
    public function name(): string;
    public function checkout(Order $order): PaymentResponse;
    public function getPayment(string $transactionId): Payment;
}
```

Providers are registered in `AppServiceProvider::boot()` using:
```php
PaymentProvider::extend('mollie', function () { ... });
```

### Domain Models

- **Organizer**: Multi-tenant entity with `payment_provider` field determining which payment system to use
- **Ticket**: Belongs to an Organizer, uses `moneyphp/money` for price handling (stored in cents)
- **Order**: Created as "pending", then completed/canceled via webhook callbacks

### Payment Flow

1. `CheckoutController::redirectToPaymentProvider()` creates a pending Order
2. `PaymentProviderFactory::createForOrganizer()` resolves the correct provider
3. Provider's `checkout()` returns a `PaymentResponse` with redirect URL
4. Webhook controllers (`app/Payment/{Provider}/Controllers/WebhookController.php`) handle payment status updates

### Key Configurations

- Payment provider API keys: `config/payment.php` (uses `STRIPE_SECRET_KEY`, `MOLLIE_API_KEY` env vars)
- Share URL for local webhook testing: `SHARE_URL` env var (use with `./vendor/bin/sail share`)

## Testing

Tests use SQLite in-memory database. Fake payment providers are available in `tests/Fake/` for testing without external API calls.

## Git Workflow

Never merge development into feature branches.
- always use snakecase for testing methods