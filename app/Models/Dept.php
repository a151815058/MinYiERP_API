<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dept extends Model
{
    use HasFactory;

    protected $table = 'depts'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time

    protected $fillable = [
        'uuid', 'dept_no', 'dept_nm', 'note', 'is_valid', 'create_user', 'create_time', 'update_user', 'update_time'
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
    public static function findByDeptNo($deptNo)
    {
        return self::where('dept_no', $deptNo)->first();
    }

     // 🔍 查詢所有有效部門
    public static function getValidDepts()
    {
        return self::where('is_valid', '1')->get();
    }

    // 🔹 多對多關係：一個部門可以有多個使用者
    public function sysusers()
    {
        return $this->belongsToMany(SysUser::class, 'sysuser_depts', 'dept_id', 'user_id')
                ->withPivot('is_valid', 'create_user', 'create_time', 'update_user', 'update_time'); // 取出附加欄位;
    }


}