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
        Schema::create('mm_city', function (Blueprint $table) {
            $table->uuid('id')->comment('KEY')->primary();
            $table->string(column: 'cityidnew')->comment('縣市代碼')->unique();
            $table->string('cityname5')->comment('縣市名稱');
            $table->integer('cityorder')->comment('排序');
            $table->integer('bid')->comment('BID');
            $table->integer('counid')->comment('COUNID');
            $table->float('lon')->comment('經度');
            $table->float('lat')->comment('緯度');
            $table->integer('sortorder')->comment('排序');
            $table->integer('fivecity')->comment('五都縣市');
            $table->integer('citycode')->comment('縣市代碼');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mm_city');
    }
};
