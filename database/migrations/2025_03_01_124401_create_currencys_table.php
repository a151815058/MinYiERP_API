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
        Schema::create('currencys', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary();
            $table->string('currency_no')->comment('貨幣代碼')->unique();
            $table->string('currency_nm')->comment('貨幣名稱');
            $table->string('currency_rate')->comment('現在匯率(以台幣為基準)')->nullable();
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
        Schema::dropIfExists('currencys');
    }
};
