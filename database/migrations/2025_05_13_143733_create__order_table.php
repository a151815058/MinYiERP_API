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
        Schema::create('Order', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary()->unique();
            $table->string('order_type')->comment('訂單單別');
            $table->string('order_no')->comment('訂單單號');
            $table->date('order_date')->comment('訂單日期');
            $table->string('customer_name')->comment('客戶名稱');
            $table->string('contact_person')->comment('聯絡人');
            $table->string('expected_completion_date')->comment('預計完成日');
            $table->string('responsible_dept')->comment('負責部門');
            $table->string('responsible_staff')->comment('負責業務');
            $table->string('terms_no')->comment('付款條件代碼');
            $table->string('currency_no')->comment('幣別');
            $table->string('tax_type')->comment('課稅別');
            $table->string('is_deposit')->comment('是否開立訂金 0:開立訂金 1:不開立訂金')->default(0);
            $table->string('create_deposit_type')->comment('開立訂金類型 0:不開立 1:百分比 2:金額')->default(0);
            $table->string('customer_address')->comment('客戶地址');
            $table->string('delivery_address')->comment('送貨地址');
            $table->string('status')->comment('狀態 待簽核:0 已簽核:1 已結案:2 已取消:3')->default(0);
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
        Schema::dropIfExists('Order');
    }
};
