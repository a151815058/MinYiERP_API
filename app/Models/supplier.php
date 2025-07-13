<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier'; // 明確指定資料表名稱

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // 因為我們手動使用 CreateTime 和 update_time

    protected $fillable = [
        'uuid',
        'supplier_no',
        'supplier_shortnm',
        'supplier_fullnm',
        'supplier_type',
        'Classification',
        'responsible_person',
        'contact_person',
        'zipcode1',
        'city_id',
        'town_id',
        'address1',
        'zipcode2',
        'city_id2',
        'town_id2',             
        'address2',
        'currencyid',
        'payment_termid',
        'phone',
        'phone2',
        'fax',
        'mobile_phone',
        'contact_email',
        'user_id',
        'account_category',
        'invoice_title',
        'taxid',
        'tax_type',
        'established_date',
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

    // 🔍 透過 supplierNo 查詢    // 🔍 透過 supplierNo 查詢付款條件
    public static function findBysupplierNo($supplierNo)
    {
        return static::where('supplier_no', $supplierNo)->first();
    }

     // 🔍 查詢所有有效供應商資料
    public static function getValidsuppliers()
    {
        return static::where('is_valid', operator: '1')->get();
    }


}
