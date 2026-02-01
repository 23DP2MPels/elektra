<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'preces';
    protected $primaryKey = 'preces_id';

    protected $fillable = [
        'nosaukums',
        'apraksts',
        'razotajs',
        'modelis',
        'attels_url',
        'kategorijas_id',
    ];

    protected $appends = ['current_price', 'image_url', 'name', 'id'];

    public function getIdAttribute()
    {
        return $this->preces_id;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'kategorijas_id', 'kategorijas_id');
    }

    public function specifications()
    {
        return $this->hasMany(Specification::class, 'preces_id');
    }

    public function priceHistory()
    {
        return $this->hasMany(PriceHistory::class, 'preces_id');
    }

    public function trackedByUsers()
    {
        return $this->belongsToMany(
            User::class,
            'sekotas_preces',
            'preces_id',
            'lietotaja_id'
        )->withPivot('target_price')->withTimestamps();
    }

    public function corrections()
    {
        return $this->hasMany(Correction::class, 'preces_id');
    }

    public function getCurrentPriceAttribute()
    {
        $latestPrice = $this->priceHistory()
            ->latest()
            ->first();

        return $latestPrice ? $latestPrice->cena : null;
    }

    public function getImageUrlAttribute()
    {
        return $this->attels_url;
    }

    public function getNameAttribute()
    {
        return $this->nosaukums;
    }

    public function store()
    {
        $latestPrice = $this->priceHistory()
            ->latest()
            ->with('store')
            ->first();

        return $latestPrice ? $latestPrice->store() : null;
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('kategorijas_id', $categoryId);
    }

    public function scopeByManufacturer($query, $manufacturer)
    {
        return $query->where('razotajs', $manufacturer);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nosaukums', 'like', "%{$search}%")
              ->orWhere('apraksts', 'like', "%{$search}%")
              ->orWhere('razotajs', 'like', "%{$search}%")
              ->orWhere('modelis', 'like', "%{$search}%");
        });
    }
}
