<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;

    protected $table = 'account'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time

    protected $fillable = [
        'uuid',
        'account_no',
        'account_name',
        'Puuid',
        'tier',
        'dc',
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

    // 🔍 透過 AccNo 查詢    // 🔍 透過 AccNo 查詢付款條件
    public static function findByAccNo($AccNo)
    {
        return self::where('account_no', $AccNo)->first();
    }

     // 🔍 查詢所有有效付款條件
    public static function getValidAccount()
    {
        return self::where('is_valid', '1')->get();
    }
}
