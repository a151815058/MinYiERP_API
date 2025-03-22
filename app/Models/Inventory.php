<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 UpdateTime

    protected $fillable = [
        'uuid', 'InventoryNO', 'InventoryNM', 'InventoryQty','Safety_stock','LastStockReceiptDate', 'IsVaild', 'Createuser', 'UpdateUser', 'CreateTime', 'UpdateTime'
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

    // 🔍 透過 InventoryNO 查詢    // 🔍 透過 InventoryNO 查詢部門
    public static function findByInventoryNO($InventoryNO)
    {
        return self::where('InventoryNO', $InventoryNO)->first();
    }

     // 🔍 查詢所有有效庫別
    public static function getValidInventory()
    {
        return static::where('IsVaild', '1')->get();
    }
}