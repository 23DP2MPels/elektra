<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'veikali';
    protected $primaryKey = 'veikala_id';

    protected $fillable = ['nosaukums', 'url', 'logo_url'];
    protected $appends = ['name'];

    public function priceHistory()
    {
        return $this->hasMany(PriceHistory::class, 'veikala_id');
    }

    public function getNameAttribute()
    {
        return $this->nosaukums;
    }
}
