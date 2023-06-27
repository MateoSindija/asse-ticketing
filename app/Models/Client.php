<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

class Client extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'clients';

    protected $primaryKey = "id";
    protected $fillable = ["first_name", "last_name", "email", "phone"];


    public function ticket(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopeSearchClients(
        $query,
        ?string $q,

    ): Collection {

        if ($q == "") {
            return [];
        }

        $clients = Client::where(function ($query) use ($q) {
            $query->orWhere('first_name', 'ILIKE', '%' . $q . '%')
                ->orWhere('last_name', 'ILIKE', '%' . $q . '%')
                ->orWhere('email', 'ILIKE', '%' . $q . '%')
                ->orWhere('phone', 'ILIKE', '%' . $q . '%')
                ->orWhereRaw('CONCAT("clients"."first_name",' . "' '" . ', "clients"."last_name") ILIKE ' . "'%$q%'");
        });

        return $clients->orderBy("created_at", "desc")->get();
    }

    protected static function booted(): void
    {
        static::deleting(function (Client $client) {
            $ticket_ids = $client->ticket()->where("client_id", $client->id)->pluck("id");
            Ticket::destroy($ticket_ids);
        });
    }
}
