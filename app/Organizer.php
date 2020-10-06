<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Ticket;

class Organizer extends Model
{
    protected $fillable = [
        'name', 'payment_provider', 'payment_provider_config'
    ];

    protected $casts = [
        'payment_provider_config' => 'array'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
