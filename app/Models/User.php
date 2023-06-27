<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract

{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, HasFactory, HasUuids, Notifiable;

    protected $table = 'users';
    protected $primaryKey = "id";
    protected $fillable = ["first_name", "last_name", "email", "password"];
    protected $hidden = ['password', 'remember_token'];


    public function ticket(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeSearchUsers(
        $query,
        ?string $q,

    ): Collection {

        if ($q == "") {
            return [];
        }

        $users = User::where(function ($query) use ($q) {
            $query->orWhere('first_name', 'ILIKE', '%' . $q . '%')
                ->orWhere('last_name', 'ILIKE', '%' . $q . '%')
                ->orWhere('email', 'ILIKE', '%' . $q . '%')
                ->orWhereRaw('CONCAT("users"."first_name",' . "' '" . ', "users"."last_name") ILIKE ' . "'%$q%'");
        });

        return $users->get();
    }
}
