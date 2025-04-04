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
        Schema::create('billinfo', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary();
            $table->string('BillNo')->comment('單據代號')->unique();
            $table->string('BillNM')->comment('單據名稱');
            $table->string('BillType')->comment('單據類型');
            $table->string('BillEncode')->comment('單據編碼方式');
            $table->integer('BillCalc')->comment('單據計算方式');
            $table->integer('AutoReview')->comment('是否自動核准');
            $table->string('GenOrder')->comment('自動產生銷貨單');
            $table->integer('OrderType')->comment('銷貨單別');
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
        Schema::dropIfExists('billinfo');
    }
};
