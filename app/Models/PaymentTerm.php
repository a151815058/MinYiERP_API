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
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 UpdateTime

    protected $fillable = [
        'Uuid', 'Puuid', 'TermsNo', 'TermsNM', 'TermsDays','PayMode','PayDay','Note', 'Createuser', 'UpdateUser', 'CreateTime', 'UpdateTime'
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
        return self::where('TermsNo', $TermsNo)->first();
    }

     // 🔍 查詢所有有效付款條件
    public static function getValidTerms()
    {
        return self::where('IsVaild', '1')->get();
    }
}
