<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
#[Fillable(['rol_id', 'procurador_id', 'users_nom', 'email', 'users_contra', 'users_estado'])]
#[Hidden(['users_contra', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'users_id';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'users_contra' => 'hashed', 
        ];
    }
    public function getAuthPassword()
    {
        return $this->users_contra;
    }
    public function attorney(): BelongsTo
    {
        return $this->belongsTo(Attorney::class, 'attorney_id', 'attorney_id');
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rol_id', 'rol_id');
    }
}