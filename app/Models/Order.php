<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\OrderCompleted;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;
use Money\Money;
use App\Payment\Contracts\Payment;
use RuntimeException;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id', 'ticket_id', 'email', 'payment_provider', 'payment_transaction_id', 'payment_status', 'status'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4();
        });
    }

    public static function pending(Ticket $ticket, string $email): self
    {
        return static::create([
            'ticket_id' => $ticket->id,
            'status' => 'pending',
            'email' => $email
        ]);
    }

    public function complete(): void
    {
        if (!$this->isPending()) {
            throw new RuntimeException('Only `pending` orders can be completed');
        }

        $this->update(['status' => 'completed']);

        event(new OrderCompleted($this));
    }

    public function cancel(): void
    {
        $this->update(['status' => 'canceled']);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function processPayment(Payment $payment): void
    {
        $this->update([
            'payment_transaction_id' => $payment->transactionId(),
            'payment_status' => $payment->status()
        ]);

        if ($payment->isCompleted()) {
            $this->complete();
        }

        if ($payment->isCanceled()) {
            $this->cancel();
        }
    }

    public static function findByTransactionId(string $transactionId): self
    {
        return static::where('payment_transaction_id', $transactionId)->first();
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getReferenceAttribute(): string
    {
        return str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    public function getTotalAttribute(): Money
    {
        return $this->ticket->price;
    }

    public function getQrCodeUrlAttribute(): string
    {
        $data = sprintf('https://www.google.be/search?q=%s', urlencode($this->ticket->title));
        $url = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=%s', urlencode($data));

        return $url;
    }
}
