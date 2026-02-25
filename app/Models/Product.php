<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\StockLog;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'brand_id',
        'admin_id',
        'price',
        'quantity',
        'description',
        'image',
        'code',
        'damaged_quantity',
        'defective_quantity',
        'low_stock_threshold',
        'good_stock_threshold',
        'overstock_threshold'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function logs()
    {
        return $this->hasMany(StockLog::class);
    }
}
