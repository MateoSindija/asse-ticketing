<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Client extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'client';

    protected $primaryKey = "id";
    protected $fillable = ["first_name", "last_name", "email", "phone"];


    public function ticket(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Ticket $ticket) {
            $ticket->delete();
        });
    }
}
