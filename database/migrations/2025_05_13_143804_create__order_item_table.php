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
        Schema::create('OrderItem', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary()->unique();
            $table->string('order_id')->comment('對應 order.uuid');
            $table->string('line_no')->comment('序號');
            $table->string('product_no')->comment('品號');
            $table->string('product_nm')->comment('品名');
            $table->string('specification')->comment('規格');
            $table->string('inventory_no')->comment('出庫倉別');
            $table->string('qty')->comment('數量');
            $table->string('unit')->comment('單位');
            $table->string('lot_num')->comment('批號');
            $table->string('unit_price')->comment('單價');
            $table->string('amount')->comment('金額');
            $table->string('customer_product_no')->comment('客戶品號');
            $table->string('note')->comment('備註');
            $table->string('status')->comment('狀態 未結案:0 已結案:1 指定結案:2 已作廢:3')->default(0);
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
        Schema::dropIfExists('OrderItem');
    }
};
