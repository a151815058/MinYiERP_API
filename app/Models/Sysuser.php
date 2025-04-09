<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sysuser extends Model
{
    use HasFactory;

    protected $table = 'sysusers'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time

    protected $fillable = [
        'uuid',
        'user_no',
        'user_nm',
        'note',
        'is_valid',
        'create_user',
        'create_time',
        'update_user',
        'update_time'
    ];    

    // 🔹 多對多關係：一個使用者可以屬於多個部門
    public function depts()
    {
        return $this->belongsToMany(Dept::class, 'sysuser_depts', 'user_id', 'dept_id')
                ->withPivot('is_valid','create_user', 'create_time','update_user', 'update_time'); // 取出附加欄位;
    }

    public static function getValidusers()
    {
        return self::where('is_valid', operator: '1')->get();
    }

}
