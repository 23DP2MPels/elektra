<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correction extends Model
{
    use HasFactory;

    protected $table = 'labojumi';
    protected $primaryKey = 'labojuma_id';

    protected $fillable = [
        'lietotaja_id',
        'preces_id',
        'labojuma_teksts',
        'statuss',
        'apstiprinaja_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'lietotaja_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'preces_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'apstiprinaja_id');
    }

    public function scopePending($query)
    {
        return $query->where('statuss', 'iesniegts');
    }

    public function scopeApproved($query)
    {
        return $query->where('statuss', 'apstiprināts');
    }

    public function scopeRejected($query)
    {
        return $query->where('statuss', 'noraidīts');
    }
}
