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
        Schema::create('mm_town', function (Blueprint $table) {
            $table->uuid('id')->comment('KEY')->primary();
            $table->string(column: 'townid')->comment('鄉鎮區代碼');
            $table->string('townname')->comment('鄉鎮區名稱');
            $table->string('postid')->comment('郵遞區號');
            $table->string('town5')->comment('鄉鎮區名稱');
            $table->float('X')->comment('X座標')->nullable();
            $table->float('Y')->comment('Y座標')->nullable();
            $table->float('lon')->comment('經度')->nullable();
            $table->float('lat')->comment('緯度')->nullable();
            $table->string('cityid5')->comment('縣市ID');
            $table->string('townid5')->comment('鄉鎮市區ID')->nullable();
            $table->string('cityname')->comment('縣市名稱');
            $table->string('citycode')->comment('縣市CODE');
            $table->string('towncode')->comment('鄉鎮市區CODE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mm_town');
    }
};
