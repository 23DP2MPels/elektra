<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'lietotaji';
    protected $primaryKey = 'lietotaja_id';

    protected $fillable = [
        'vards',
        'epasts',
        'parole',
        'loma',
        'sanemt_pazinojumus',
        'tiesibu_limenis',
    ];

    protected $hidden = [
        'parole',
        'remember_token',
    ];

    protected $appends = ['name', 'email'];

    public function getNameAttribute()
    {
        return $this->vards;
    }

    public function getEmailAttribute()
    {
        return $this->epasts;
    }

    protected $casts = [
        'sanemt_pazinojumus' => 'boolean',
        'tiesibu_limenis' => 'integer',
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->parole;
    }

    public function trackedProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'sekotas_preces',
            'lietotaja_id',
            'preces_id'
        )->withPivot('target_price')->withTimestamps();
    }

    public function corrections()
    {
        return $this->hasMany(Correction::class, 'lietotaja_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'lietotaja_id');
    }

    public function isAdmin()
    {
        return $this->loma === 'administrators';
    }

    public function isEditor()
    {
        return in_array($this->loma, ['redaktors', 'administrators']);
    }

    public function isRegisteredUser()
    {
        return in_array($this->loma, ['registrets_lietotajs', 'redaktors', 'administrators']);
    }
}
