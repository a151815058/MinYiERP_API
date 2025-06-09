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
            $table->string('client_no',20)->comment(comment: '客戶編號')->unique();
            $table->string('client_shortnm',50)->comment(comment: '客戶簡稱');
            $table->string('client_fullnm',255)->comment(comment: '客戶全名');
            $table->string('client_type',255)->comment(comment: '客戶型態 (個人客戶、企業客戶、政府客戶)')->default('1');
            $table->string('responsible_person',10)->comment(comment: '負責人')->nullable();
            $table->string('contact_person',10)->comment(comment: '聯絡人')->nullable();
            $table->string('zip_code1',10)->comment(comment: '郵遞區號 1')->nullable();
            $table->string('address1',255)->comment(comment: '公司地址 1')->nullable();
            $table->string('zip_code2',10)->comment(comment: '郵遞區號 2');
            $table->string('address2',255)->comment(comment: '送貨地址');
            $table->string('currency_id',255)->comment(comment: '幣別')->nullable();
            $table->string('paymentterm_id',255)->comment(comment: '付款條件')->nullable();
            $table->string('phone',20)->comment(comment: '公司電話')->nullable();
            $table->string('fax',20)->comment(comment: '公司傳真')->nullable();
            $table->string('mobile_phone',20)->comment(comment: '聯絡人行動電話')->nullable();
            $table->string('contact_email',255)->comment(comment: '聯絡人信箱')->nullable();
            $table->string('user_id',255)->comment(comment: '業務人員')->nullable();
            $table->string('account_category',255)->comment(comment: '科目別')->nullable();
            $table->string('invoice_title',100)->comment(comment: '發票抬頭');
            $table->string('taxid',8)->comment(comment: '統一編號');
            $table->string('taxtype',255)->comment(comment: '課稅別')->nullable();
            $table->string('delivery_method',255)->comment(comment: '發票寄送方式');
            $table->string('recipient_name',255)->comment(comment: '發票收件人')->nullable();
            $table->string('invoice_address',255)->comment(comment: '發票地址')->nullable();
            $table->string('recipient_phone',20)->comment(comment: '聯絡電話2')->nullable();
            $table->string('recipient_email',255)->comment(comment: '發票收件信箱')->nullable();
            $table->dateTime('established_date')->comment(comment: '成立時間')->nullable();
            $table->string('note',255)->comment('備註')->nullable();
            $table->string('is_valid')->comment('是否有效 0:失效 1:有效')->default(1);
            $table->string('create_user')->comment('建立人員')->default('admin');
            $table->dateTime('create_time')->comment('建立時間');
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
