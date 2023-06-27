<?php

namespace App\Models;

use App\Traits\SelfReferenceTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;

class Comment extends Model
{
    use HasFactory, HasUuids, SelfReferenceTrait;


    protected $table = 'comments';
    protected $primaryKey = "id";
    protected $fillable = ["comment", "ticket_id", "user_id", "parent_id"];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function scopeGetComments($query, string $ticket_id): Collection
    {
        return Comment::where("ticket_id", $ticket_id)
            ->whereNull('parent_id')
            ->orderBy("comments.created_at", "ASC")
            ->get();
    }


    protected static function booted(): void
    {
        static::deleting(function (Comment $comment) {
            $comment->children()->delete();
        });
    }
}
