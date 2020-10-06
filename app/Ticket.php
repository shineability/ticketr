<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Money\Money;

class Ticket extends Model
{
    protected $fillable = [
        'organizer_id', 'title', 'price'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4();
        });
    }

    public function organizer()
    {
        return $this->belongsTo('App\Organizer');
    }

    public function getPriceAttribute($value): Money
    {
        return Money::EUR($value);
    }

    public function getFormattedPriceAttribute()
    {
        return app('money.formatter')->format($this->price);
    }
}
