<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use OwenIt\Auditing\Contracts\Auditable;

class Fuerzas extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'idfuerza';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fuerza',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function centros_reclutamiento()
    {
        return $this->hasMany(CentrosReclutamiento::class, 'id_fuerza', 'idfuerza');
    }
}
