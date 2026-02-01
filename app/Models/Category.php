<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'kategorijas';
    protected $primaryKey = 'kategorijas_id';

    protected $fillable = [
        'nosaukums',
        'apraksts',
        'vecaka_kategorijas_id',
    ];

    protected $appends = ['name'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'vecaka_kategorijas_id', 'kategorijas_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'vecaka_kategorijas_id', 'kategorijas_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'kategorijas_id');
    }

    public function getNameAttribute()
    {
        return $this->nosaukums;
    }
}
