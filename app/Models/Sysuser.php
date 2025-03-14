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
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 UpdateTime

    protected $fillable = [
        'uuid', 'UsrNo', 'UsrNM', 'Note', 'IsVaild', 'Createuser', 'UpdateUser', 'CreateTime', 'UpdateTime'
    ];

    // 🔹 多對多關係：一個使用者可以屬於多個部門
    public function depts()
    {
        return $this->belongsToMany(Dept::class, 'sysuser_depts', 'User_id', 'Dept_id')
                ->withPivot('IsVaild','Createuser', 'CreateTime','UpdateUser', 'UpdateTime'); // 取出附加欄位;
    }

    public static function getValidusers()
    {
        return self::where('IsVaild', operator: '1')->get();
    }

}
