<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MMcity extends Model
{
    use HasFactory;

    protected $table = 'mm_city'; // 明確指定資料表名稱

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time
    protected $fillable = [
        'id',
        'cityidnew',
        'cityname5',
        'cityorder',
        'bid',
        'counid',
        'lon',
        'lat',
        'sortorder',
        'fivecity',
        'citycode'
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
