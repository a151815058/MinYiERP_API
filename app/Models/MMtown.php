<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MMtown extends Model
{
    use HasFactory;

    protected $table = 'mm_town'; // 明確指定資料表名稱

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time
    protected $fillable = [
        'id',
        'townid',
        'townname',
        'postid',
        'town5',
        'X',
        'Y',
        'lon',
        'lat',
        'cityid5',
        'townid5',
        'cityname',
        'citycode',
        'towncode'
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
