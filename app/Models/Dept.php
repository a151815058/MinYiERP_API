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
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 UpdateTime

    protected $fillable = [
        'uuid', 'DeptNo', 'DeptNM', 'Note', 'IsVaild', 'Createuser', 'UpdateUser', 'CreateTime', 'UpdateTime'
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
        return self::where('DeptNo', $deptNo)->first();
    }

     // 🔍 查詢所有有效部門
    public static function getValidDepts()
    {
        return self::where('IsVaild', '1')->get();
    }

    // 🔹 多對多關係：一個部門可以有多個使用者
    public function sysusers()
    {
        return $this->belongsToMany(SysUser::class, 'sysuser_depts', 'Dept_id', 'User_id')
                ->withPivot('IsVaild','Createuser', 'CreateTime','UpdateUser', 'UpdateTime'); // 取出附加欄位;
    }


}