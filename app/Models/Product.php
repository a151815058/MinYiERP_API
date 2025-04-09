<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time

    protected $fillable = [
        'uuid', 'product_no', 'product_nm', 'specification', 'price_1', 'price_2', 'price_3',
        'cost_1', 'cost_2', 'cost_3', 'batch_control', 'valid_days', 'effective_date',
        'stock_control', 'safety_stock', 'expiry_date', 'description', 'is_valid',
        'create_user', 'create_time', 'update_user', 'update_time'
    ]; 
	    
    // 自動生成 UUID
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    // 🔍 透過 ProductNO 查詢    // 🔍 透過 ProductNO 查詢品號
    public static function findByProductNO($ProductNO)
    {
        return self::where('product_no', $ProductNO)->first();
    }

     // 🔍 查詢所有有效品號
    public static function getValidProducts()
    {
        return self::where('is_valid', 1)->get();
    }
}
