<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentTerm extends Model
{
    use HasFactory;

    protected $table = 'paymentterms'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time

    protected $fillable = [
        'uuid',
        'terms_no',
        'terms_nm',
        'terms_days',
        'pay_mode',
        'pay_day',
        'note',
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

    // 🔍 透過 TermsNo 查詢    // 🔍 透過 TermsNo 查詢付款條件
    public static function findByTermsNo($TermsNo)
    {
        return self::where('terms_no', $TermsNo)->first();
    }

     // 🔍 查詢所有有效付款條件
    public static function getValidTerms()
    {
        return self::where('is_valid', '1')->get();
    }
}
