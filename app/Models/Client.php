<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

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
        static::deleting(function (Client $client) {
            $ticket_ids = $client->ticket()->where("client_id", $client->id)->pluck("id");
            Ticket::destroy($ticket_ids);
        });
    }
}
