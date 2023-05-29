<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Ticket extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'ticket';
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

    protected static function booted(): void
    {
        static::deleting(function (Ticket $ticket) {
            $ticket->comment()->delete();
        });
    }
}
