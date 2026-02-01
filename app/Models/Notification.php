<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'pazinojumi';
    protected $primaryKey = 'pazinojuma_id';

    protected $fillable = [
        'lietotaja_id',
        'cenas_id',
        'zinojums',
        'izlasits',
    ];

    protected $casts = [
        'izlasits' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'lietotaja_id');
    }

    public function priceHistory()
    {
        return $this->belongsTo(PriceHistory::class, 'cenas_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('izlasits', false);
    }
}
