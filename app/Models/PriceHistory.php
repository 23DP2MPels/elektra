<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    use HasFactory;

    protected $table = 'cenu_vesture';
    protected $primaryKey = 'cenas_id';

    protected $fillable = [
        'preces_id',
        'veikala_id',
        'cena',
        'iepriekšējā_cena',
        'url',
        'pieejams',
    ];

    protected $casts = [
        'cena' => 'decimal:2',
        'iepriekšējā_cena' => 'decimal:2',
        'pieejams' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'preces_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'veikala_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'cenas_id');
    }
}
