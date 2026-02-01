<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    use HasFactory;

    protected $table = 'specifikacijas';
    protected $primaryKey = 'specifikacijas_id';

    protected $fillable = ['preces_id', 'parametrs', 'vertiba'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'preces_id');
    }
}
