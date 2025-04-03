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
        Schema::create('Inventory', function (Blueprint $table) {
            $table->uuid('Uuid')->comment('KEY')->primary();
            $table->string('InventoryNO')->comment('庫別代號')->unique();
            $table->string('InventoryNM')->comment('庫別名稱');
            $table->decimal('InventoryQty')->comment('庫存數量')->default(0);
            $table->string('LotNum')->comment('批號')->nullable();
            $table->decimal('Safety_stock')->comment('安全庫存')->default(0);
            $table->date('LastStockReceiptDate')->comment('最近一次進貨日')->nullable();
            $table->boolean('IsValid')->comment('是否有效')->default(1);
            $table->string('Createuser')->comment('建立人員');
            $table->dateTime('CreateTime')->comment('建立時間')->nullable();
            $table->string('UpdateUser')->comment('異動人員');
            $table->dateTime('UpdateTime')->comment('異動時間')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Inventory');
    }
};
