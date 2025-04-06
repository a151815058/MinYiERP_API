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
        Schema::create('Product', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary()->unique();
            $table->string('product_no')->comment('品號')->unique();
            $table->string('product_nm')->comment('品名');
            $table->string('specification')->comment('規格');
            $table->decimal('price_1')->comment('售價一');
            $table->decimal('price_2')->comment('售價二')->nullable();
            $table->decimal('price_3')->comment('售價三')->nullable();
            $table->decimal('cost_1')->comment('進價一');
            $table->decimal('cost_2')->comment('進價二')->nullable();
            $table->decimal('cost_3')->comment('進價三')->nullable();
            $table->boolean('batch_control')->comment('批號管理')->default(1);
            $table->integer('valid_days')->comment('有效天數');
            $table->date('effective_date')->comment('生效日期');
            $table->boolean('stock_control')->comment('是否庫存管理')->default(1);
            $table->integer('safety_stock')->comment('安全庫存');
            $table->date('expiry_date')->comment('失效日期');
            $table->string(column: 'description')->comment('商品描述')->nullable();
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
        Schema::dropIfExists('Product');
    }
};
