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
            $table->uuid('uuid')->comment('KEY')->primary();
            $table->string('TermsNo')->comment('付款條件代碼')->unique();
            $table->string('TermsNM')->comment('付款條件名稱');
            $table->integer('TermsDays')->comment('付款條件月結天數');
            $table->string('PayMode')->comment('付款條件 當月/隔月');
            $table->integer('PayDay')->comment('付款時間');
            $table->string('Note')->comment('備註')->nullable();
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
        Schema::dropIfExists('paymentterms');
    }
};
