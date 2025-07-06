<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    
    use HasFactory;
    use HasApiTokens, Notifiable;

    protected $table = 'user'; // 明確指定資料表名稱

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'username',
        'useraccount',
        'mail',
        'password_hash',
        'is_valid',
        'create_user',
        'create_time',
        'update_user',
        'update_time'
    ];


}
