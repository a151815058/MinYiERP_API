<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supplier', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary()->unique();
            $table->string('supplier_no')->comment(comment: '供應商編號')->unique();
            $table->string('supplier_shortnm')->comment(comment: '供應商簡稱');
            $table->string('supplier_fullnm')->comment(comment: '供應商全名');
            $table->string('supplier_type')->comment(comment: '供應商類型 (公司、個體戶、外商等)');
            $table->string('Classification')->comment(comment: '供應商分類(原物料、零件、服務、代理商)');
            $table->string('responsible_person')->comment(comment: '負責人')->nullable();
            $table->string('contact_person')->comment(comment: '聯絡人')->nullable();
            $table->string('zipcode1')->comment(comment: '郵遞區號 1')->nullable();
            $table->string('city_id',255)->comment(comment: '縣市')->nullable();
            $table->string('town_id',255)->comment(comment: '區域')->nullable();
            $table->string('address1')->comment(comment: '公司地址 1')->nullable();
            $table->string('zipcode2')->comment(comment: '郵遞區號 2');
            $table->string('city_id2',255)->comment(comment: '縣市2');
            $table->string('town_id2',255)->comment(comment: '區域2');             
            $table->string('address2')->comment(comment: '公司地址 2');
            $table->string('currencyid')->comment(comment: '幣別 ')->nullable();
            $table->string('payment_termid')->comment(comment: '付款條件')->nullable();
            $table->string('phone')->comment(comment: '公司電話')->nullable();
            $table->string('phone2')->comment(comment: '聯絡電話2')->nullable();
            $table->string('fax')->comment(comment: '公司傳真')->nullable();
            $table->string('mobile_phone')->comment(comment: '行動電話')->nullable();
            $table->string('contact_email')->comment(comment: '聯絡人信箱')->nullable();
            $table->string('user_id')->comment(comment: '業務人員')->nullable();
            $table->string('account_category',255)->comment(comment: '科目別')->nullable();
            $table->string('invoice_title',100)->comment(comment: '發票抬頭');
            $table->string('taxid')->comment(comment: '統一編號');
            $table->string('tax_type')->comment(comment: '課稅別')->nullable();
            $table->dateTime('established_date')->comment(comment: '成立時間');
            $table->string('is_valid')->comment('是否有效 0:失效 1:有效')->default(1);
            $table->string('create_user')->comment('建立人員')->default('admin');
            $table->dateTime('create_time')->comment('建立時間')->default(now());
            $table->string('update_user')->comment('異動人員')->nullable();
            $table->dateTime('update_time')->comment('異動時間')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};
