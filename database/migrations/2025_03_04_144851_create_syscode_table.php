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
        Schema::create('syscode', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('Puuid')->nullable();
            $table->string('Paramcode');
            $table->string('Param');
            $table->string('Note')->nullable();
            $table->boolean('IsValid')->default(1);
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
        Schema::dropIfExists('syscode');
    }
};
