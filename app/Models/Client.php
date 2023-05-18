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

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullName()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function ticket(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
