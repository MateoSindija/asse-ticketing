<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'tickets';
    protected $primaryKey = "id";
    protected $fillable = ["status", "title", "description", "user_id", "client_id"];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeGetTicketsWithStatusAndDate(
        $query,
        ?string $status,
        ?string $entries,
        ?string $start_date,
        ?string $end_date,
        ?string $client_id,
    ): LengthAwarePaginator {
        $query = Ticket::query();

        $query->with("client", "user");


        if (!$client_id) {
            $query->when($status && $status != "all", function ($query) use ($status) {
                if ($status == "mine") {
                    $userID = Auth::id();
                    return $query->where("user_id", $userID);
                }
                return $query->where("status", $status);
            });
            $query->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween("created_at", [$start_date, $end_date]);
            });
        } else {
            $query->where("client_id", $client_id);
        }

        return $query->orderBy("created_at", "DESC")->paginate($entries ? $entries : 20);
    }

    public function scopeSearchTicket(
        $query,
        string $search,
        ?string $status = "all",
        ?string $entries = "20",
        ?string $start_date,
        ?string $end_date
    ): LengthAwarePaginator {
        return Ticket::with("client", "user")
            ->when($status != "all", function ($query) use ($status) {
                return $query->when($status == "mine", function ($query) {
                    $userID = Auth::id();
                    return $query->where("tickets.user_id", $userID);
                }, function ($query) use ($status) {
                    return $query->where("status", $status);
                });
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween("tickets.created_at", [$start_date, $end_date]);
            })
            ->where(function ($query) use ($search) {
                $query->orWhereHas('user', function ($query) use ($search) {
                    $query->where('first_name', 'ILIKE', '%' . $search . '%')
                        ->orWhere('last_name', 'ILIKE', '%' . $search . '%')
                        ->orWhereRaw('CONCAT("first_name", \' \', "last_name") ILIKE ?', ['%' . $search . '%']);
                })->orWhereHas('client', function ($query) use ($search) {
                    $query->where('first_name', 'ILIKE', '%' . $search . '%')
                        ->orWhere('last_name', 'ILIKE', '%' . $search . '%')
                        ->orWhereRaw('CONCAT("first_name", \' \', "last_name") ILIKE ?', ['%' . $search . '%']);
                })->orWhere('title', 'ILIKE', '%' . $search . '%')
                    ->orWhere('description', 'ILIKE', '%' . $search . '%');
            })
            ->orderBy("created_at", "DESC")
            ->paginate($entries)
            ->setPath('');
    }

    protected static function booted(): void
    {
        static::deleting(function (Ticket $ticket) {
            $comment_ids = Comment::where("ticket_id", $ticket->id)->pluck("id");
            Comment::destroy($comment_ids);
        });
    }
}
