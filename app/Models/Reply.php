<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reply extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'reply';
    protected $primaryKey = "id";
    protected $fillable = ["reply", "comment_id", "user_id"];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }
}
