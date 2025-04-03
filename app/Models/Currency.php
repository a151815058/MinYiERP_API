<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencys'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 UpdateTime
    protected $fillable = [
        'uuid','CurrencyNo' ,'CurrencyNM',  'Note', 'IsValid', 'Createuser', 'UpdateUser', 'CreateTime', 'UpdateTime'
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

    // 🔍 透過 DeptNo 查詢    // 🔍 透過 DeptNo 查詢部門
    public static function findByCurrencyNo($CurrencyNo)
    {
        return self::where('CurrencyNo', $CurrencyNo)->first();
    }

     // 🔍 查詢所有有效部門
    public static function getValidCurrencys()
    {
        return self::where('IsValid', '1')->get();
    }

}
