<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Money\Money;
use Ramsey\Uuid\Uuid;

/**
 * @property int $id
 * @property string $uuid
 * @property int $organizer_id
 * @property string $title
 * @property int $price
 * @property string $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Organizer $organizer
 * @property-read Money $price
 * @property-read string $formatted_price
 */
class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'organizer_id', 'title', 'price', 'image',
    ];

    /**
     * The relationships that should be eager-loaded.
     */
    protected $with = ['organizer'];

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
     * Get the organizer that owns this ticket.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    /**
     * Get the price as a Money object.
     */
    public function getPriceAttribute($value): Money
    {
        return Money::EUR($value);
    }

    /**
     * Get the formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return app('money.formatter')->format($this->price);
    }
}
