<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Mail\OrderCompleted as OrderCompletedMail;
use Illuminate\Contracts\Mail\Mailer;

class SendOrderConfirmation
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(OrderCompleted $event): void
    {
        $this->mailer
            ->to($event->order->email)
            ->queue(new OrderCompletedMail($event->order));
    }
}
