<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'payment_provider'];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
