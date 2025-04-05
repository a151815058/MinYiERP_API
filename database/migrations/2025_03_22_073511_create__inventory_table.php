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
        Schema::create('inventory', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary()->unique();
            $table->string('inventory_no')->comment('庫別代號')->unique();
            $table->string('inventory_nm')->comment('庫別名稱');
            $table->decimal('inventory_qty')->comment('庫存數量')->default(0);
            $table->string('lot_num')->comment('批號')->nullable();
            $table->decimal('safety_stock')->comment('安全庫存')->default(0);
            $table->date('lastStock_receiptdate')->comment('最近一次進貨日')->nullable();
            $table->string('is_valid')->comment('是否有效')->default(1);
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
        Schema::dropIfExists('Inventory');
    }
};
