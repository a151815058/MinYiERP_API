<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BillInfo extends Model
{
    use HasFactory;

    protected $table = 'billinfo'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time
    protected $fillable = [
        'bill_no',
        'bill_nm',
        'bill_type',
        'bill_encode',
        'bill_calc',
        'auto_review',
        'gen_order',
        'gen_bill_type',
        'order_type',
        'note',
        'is_valid'
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

    // 🔍 透過 TermsNo 查詢    // 🔍 透過 TermsNo 查詢付款條件
    public static function findByBillNo($BillNo)
    {
        return self::where('bill_no', $BillNo);
    }

     // 🔍 查詢所有有效付款條件
    public static function getValidBillNos()
    {
        return self::where('is_valid', '1')->get();
    }

}
