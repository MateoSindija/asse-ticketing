<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasUuids;

    protected $table = 'messages';
    protected $primaryKey = "id";
    protected $fillable = ["user_id", "title"];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
