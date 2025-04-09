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
        'Uuid', 'supplierNo', 'supplierShortNM', 'supplierFullNM','ZipCode1', 'Address1','ZipCode2','Address2','TaxID','ResponsiblePerson','EstablishedDate','Phone','Fax','ContactPerson','ContactPhone','MobilePhone','ContactEmail','CurrencyID','TaxType','PaymentTermID','UserID','Note', 'Createuser', 'update_user', 'CreateTime', 'update_time'
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
        return static::where('supplierNo', $supplierNo)->first();
    }

     // 🔍 查詢所有有效供應商資料
    public static function getValidsuppliers()
    {
        return static::where('is_valid', operator: '1')->get();
    }


}
