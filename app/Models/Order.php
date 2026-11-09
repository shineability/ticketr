<?php

namespace App\Models;

use App\Events\OrderCompleted;
use App\Payment\Contracts\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Money\Money;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/**
 * @property int $id
 * @property string $uuid
 * @property int $ticket_id
 * @property int $organizer_id
 * @property int $quantity
 * @property string $email
 * @property string $payment_provider
 * @property string|null $payment_transaction_id
 * @property string|null $payment_status
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Ticket $ticket
 * @property-read string $reference
 * @property-read Money $total
 * @property-read string $qr_code_url
 */
class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'organizer_id', 'ticket_id', 'quantity', 'email', 'payment_provider', 'payment_transaction_id', 'payment_status', 'status',
    ];

    /**
     * The possible order statuses.
     */
    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELED = 'canceled';

    /**
     * Boot the model.
     */
    public static function boot(): void
    {
        parent::boot();

        self::creating(static function (self $model): void {
            $model->uuid = Uuid::uuid4();
        });
    }

    /**
     * Create a pending order for a ticket.
     */
    public static function pending(Ticket $ticket, string $email, int $quantity = 1): self
    {
        return static::create([
            'ticket_id' => $ticket->id,
            'quantity' => $quantity,
            'status' => self::STATUS_PENDING,
            'email' => $email,
        ]);
    }

    /**
     * Complete the order.
     *
     * @throws RuntimeException If the order is not in pending status
     */
    public function complete(): void
    {
        if (! $this->isPending()) {
            throw new RuntimeException('Only `pending` orders can be completed');
        }

        $this->update(['status' => self::STATUS_COMPLETED]);

        event(new OrderCompleted($this));
    }

    /**
     * Cancel the order.
     */
    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELED]);
    }

    /**
     * Check if the order is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the order is canceled.
     */
    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    /**
     * Check if the order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Process a payment for this order.
     */
    public function processPayment(Payment $payment): void
    {
        $this->update([
            'payment_transaction_id' => $payment->transactionId(),
            'payment_status' => $payment->status(),
        ]);

        if ($payment->isCompleted()) {
            $this->complete();
        }

        if ($payment->isCanceled()) {
            $this->cancel();
        }
    }

    /**
     * Find an order by its transaction ID.
     */
    public static function findByTransactionId(string $transactionId): ?self
    {
        return static::where('payment_transaction_id', $transactionId)->first();
    }

    /**
     * Get the ticket for this order.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the reference number for this order.
     */
    public function getReferenceAttribute(): string
    {
        return str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Get the total price for this order.
     */
    public function getTotalAttribute(): Money
    {
        return $this->ticket->price->multiply($this->quantity);
    }

    /**
     * Get the QR code URL for this order.
     */
    public function getQrCodeUrlAttribute(): string
    {
        $data = sprintf('https://www.google.be/search?q=%s', urlencode($this->ticket->title));
        $url = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=%s', urlencode($data));

        return $url;
    }
}
