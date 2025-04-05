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
        Schema::create('paymentterms', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary()->unique();
            $table->string('terms_no')->comment('付款條件代碼')->unique();
            $table->string('terms_nm')->comment('付款條件名稱');
            $table->integer('terms_days')->comment('付款條件月結天數');
            $table->string('pay_mode')->comment('付款條件 當月/隔月');
            $table->integer('pay_day')->comment('付款時間');
            $table->string('note')->comment('備註')->nullable();
            $table->boolean('is_valid')->comment('是否有效')->default(1);
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
        Schema::dropIfExists('paymentterms');
    }
};
