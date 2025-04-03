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
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 UpdateTime

    protected $fillable = [
        'Uuid', 'ProductNO', 'ProductNM', 'Specification','Barcode', 'Price_1','Price_2','Price_3','Cost_1','Cost_2','Cost_3','Batch_control','Valid_days','Effective_date','Stock_control','Safety_stock','Expiry_date','Description','IsValid', 'Createuser', 'UpdateUser', 'CreateTime', 'UpdateTime'
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
        return self::where('ProductNO', $ProductNO)->first();
    }

     // 🔍 查詢所有有效品號
    public static function getValidProducts()
    {
        return self::where('IsValid', 1)->get();
    }
}
