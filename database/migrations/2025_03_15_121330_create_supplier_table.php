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
            $table->uuid('uuid')->comment('KEY')->primary();
            $table->string('supplierNo')->comment(comment: '客戶編號')->unique();
            $table->string('supplierShortNM')->comment(comment: '客戶簡稱');
            $table->string('supplierFullNM')->comment(comment: '客戶全名');
            $table->string('ZipCode1')->comment(comment: '郵遞區號 1');
            $table->string('Address1')->comment(comment: '公司地址 1');
            $table->string('ZipCode2')->comment(comment: '郵遞區號 2 (選填)')->nullable();
            $table->string('Address2')->comment(comment: '公司地址 2 (選填)')->nullable();
            $table->string('TaxID')->comment(comment: '統一編號 (台灣: 8 碼)');
            $table->string('ResponsiblePerson')->comment(comment: '負責人');
            $table->dateTime('EstablishedDate')->comment(comment: '成立時間');
            $table->string('Phone')->comment(comment: '公司電話');
            $table->string('Fax')->comment(comment: '公司傳真 (選填)');
            $table->string('ContactPerson')->comment(comment: '聯絡人');
            $table->string('ContactPhone')->comment(comment: '聯絡人電話');
            $table->string('MobilePhone')->comment(comment: '聯絡人行動電話');
            $table->string('ContactEmail')->comment(comment: '聯絡人信箱');
            $table->string('CurrencyID')->comment(comment: '幣別 (幣別代號)');
            $table->string('TaxType')->comment(comment: '稅別 (應稅內含、應稅外加、免稅、零稅率等)');
            $table->string('PaymentTermID')->comment(comment: '付款條件 (付款條件代號)');
            $table->string('UserID')->comment(comment: '負責採購人員 (使用者代號)');
            $table->string('Note')->comment('備註')->nullable();
            $table->boolean('IsValid')->comment('是否有效')->default(1);
            $table->string('Createuser')->comment('建立人員')->default('admin');
            $table->dateTime('CreateTime')->comment('建立時間')->default(now());
            $table->string('UpdateUser')->comment('異動人員')->nullable();
            $table->dateTime('UpdateTime')->comment('異動時間')->nullable();
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
