<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;
use Money\Money;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id', 'title', 'price'
    ];

    protected $with = ['organizer'];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4();
        });
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo('App\Models\Organizer');
    }

    public function getPriceAttribute($value): Money
    {
        return Money::EUR($value);
    }

    public function getFormattedPriceAttribute(): string
    {
        return app('money.formatter')->format($this->price);
    }
}
