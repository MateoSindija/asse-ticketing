<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Client extends Model
{
    use HasFactory, HasUuids, Searchable;


    protected $table = 'client';

    protected $primaryKey = "id";
    protected $fillable = ["first_name", "last_name", "email", "phone"];

    public function ticket(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
