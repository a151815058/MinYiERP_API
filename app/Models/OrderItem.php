<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'orderitem'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time

    protected $fillable = [
            'uuid',
            'order_id',
            'line_no',
            'product_no',
            'product_nm',
            'specification',
            'inventory_no',
            'qty',
            'unit',
            'lot_num',
            'unit_price',
            'amount',
            'customer_product_no',
            'note',
            'status',
            'is_valid',
            'create_user',
            'create_time',
            'update_user',
            'update_time'
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
}
