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
        Schema::create('depts', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary();
            $table->string('DeptNo')->comment('部門代號')->unique();
            $table->string('DeptNM')->comment('部門名稱');
            $table->string('Note')->comment('備註')->nullable();
            $table->boolean('IsVaild')->comment('是否有效')->default(1);
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
        Schema::dropIfExists('depts');
    }
};