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
            $table->string('CurrencyNo')->comment('貨幣代碼')->unique();
            $table->string('CurrencyNM')->comment('貨幣名稱');
            $table->string('Note')->comment('備註')->nullable();
            $table->boolean('IsValid')->comment('是否有效')->default(1);
            $table->string('Createuser')->comment(comment: '建立人員');
            $table->dateTime('CreateTime')->comment(comment: '建立時間')->nullable();
            $table->string('UpdateUser')->comment(comment: '異動人員');
            $table->dateTime('UpdateTime')->comment(comment: '異動時間')->nullable();
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
