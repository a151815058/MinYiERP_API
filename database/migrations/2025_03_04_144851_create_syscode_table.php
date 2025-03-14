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
            $table->boolean('IsVaild')->default(1);
            $table->string('Createuser');
            $table->dateTime('CreateTime')->nullable();
            $table->string('UpdateUser');
            $table->dateTime('UpdateTime')->nullable();
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
