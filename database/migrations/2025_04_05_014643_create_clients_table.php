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
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary()->unique();
            $table->string('client_no')->comment(comment: '客戶編號')->unique();
            $table->string('client_shortnm')->comment(comment: '客戶簡稱');
            $table->string('client_fullnm')->comment(comment: '客戶全名');
            $table->string('zip_code1')->comment(comment: '郵遞區號 1');
            $table->string('address1')->comment(comment: '公司地址 1');
            $table->string('zip_code2')->comment(comment: '郵遞區號 2 (選填)')->nullable();
            $table->string('address2')->comment(comment: '公司地址 2 (選填)')->nullable();
            $table->string('taxid')->comment(comment: '統一編號 (台灣: 8 碼)');
            $table->string('responsible_person')->comment(comment: '負責人');
            $table->dateTime('established_date')->comment(comment: '成立時間');
            $table->string('phone')->comment(comment: '公司電話');
            $table->string('fax')->comment(comment: '公司傳真 (選填)')->nullable();
            $table->string('contact_person')->comment(comment: '聯絡人');
            $table->string('contact_phone')->comment(comment: '聯絡人電話');
            $table->string('mobile_phone')->comment(comment: '聯絡人行動電話');
            $table->string('contact_email')->comment(comment: '聯絡人信箱');
            $table->string('currency_id')->comment(comment: '幣別 (幣別代號)');
            $table->string('taxtype')->comment(comment: '稅別 (應稅內含、應稅外加、免稅、零稅率等)');
            $table->string('paymentterm_id')->comment(comment: '付款條件 (付款條件代號)');
            $table->string('user_id')->comment(comment: '負責採購人員 (使用者代號)');
            $table->string('note')->comment('備註')->nullable();
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
        Schema::dropIfExists('clients');
    }
};
