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
            $table->uuid('uuid')->comment('KEY')->primary();
            $table->string('ProductNO')->comment('品號')->unique();
            $table->string('ProductNM')->comment('品名');
            $table->string('Specification')->comment('規格');
            $table->string('Barcode')->comment('條碼')->nullable();
            $table->decimal('Price_1')->comment('售價一');
            $table->decimal('Price_2')->comment('售價二')->nullable();
            $table->decimal('Price_3')->comment('售價三')->nullable();
            $table->decimal('Cost_1')->comment('進價一');
            $table->decimal('Cost_2')->comment('進價二')->nullable();
            $table->decimal('Cost_3')->comment('進價三')->nullable();
            $table->boolean('Batch_control')->comment('批號管理')->default(1);
            $table->integer('Valid_days')->comment('有效天數');
            $table->date('Effective_date')->comment('生效日期');
            $table->boolean('Stock_control')->comment('是否庫存管理')->default(1);
            $table->integer('Safety_stock')->comment('安全庫存');
            $table->date('Expiry_date')->comment('失效日期');
            $table->string(column: 'Description')->comment('商品描述')->nullable();
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
        Schema::dropIfExists('Product');
    }
};
