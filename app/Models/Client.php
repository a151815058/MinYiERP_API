<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time
    protected $fillable = [
            'uuid',
            'client_no',
            'client_shortnm',
            'client_fullnm',
            'client_type',
            'responsible_person',
            'contact_person',
            'zip_code1',
            'address1',
            'zip_code2',
            'address2',
            'currency_id',
            'paymentterm_id',
            'phone',
            'fax',
            'mobile_phone',
            'contact_email',
            'user_id',
            'account_category',
            'invoice_title',
            'taxid',
            'taxtype',
            'delivery_method',
            'recipient_name',
            'invoice_address',
            'recipient_phone',
            'recipient_email',
            'established_date',
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

    // 🔍 透過 clientNo 查詢    // 🔍 透過 clientNo 查詢付款條件
    public static function findByclientNo($clientNo)
    {
        return static::where('client_no', $clientNo)->first();
    }

     // 🔍 查詢所有有效客戶資料
    public static function getValidClients()
    {
        return static::where('is_valid', operator: '1')->get();
    }

}
