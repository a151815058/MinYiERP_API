<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Order extends Model
{
    use HasFactory;

    protected $table = 'order'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time

    protected $fillable = [
            'uuid',
            'order_type',
            'order_no',
            'order_date',
            'customer_name',
            'contact_person',
            'expected_completion_date',
            'responsible_dept',
            'responsible_staff',
            'terms_no',
            'currency_no',
            'tax_type',
            'is_deposit',
            'create_deposit_type',
            'deposit',
            'customer_address',
            'delivery_address',
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

    public function orderitems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'uuid');
    }

    public function orderfiles()
    {
        return $this->hasMany(OrderFile::class, 'order_id', 'uuid');
    }
}
