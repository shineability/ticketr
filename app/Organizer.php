<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    protected $fillable = ['name', 'payment_provider'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
