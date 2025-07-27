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
        Schema::create('role_menus', function (Blueprint $table) {
            $table->uuid('id')->comment('KEY')->primary();
            $table->string('menu_id')->comment('menu_id');
            $table->string('role_id')->comment('role_id');
            $table->string('is_valid')->comment('是否有效 0:失效 1:有效')->default(1);
            $table->string('create_user')->comment('建立人員')->default('admin');
            $table->dateTime('create_time')->comment('建立時間')->default(now());
            $table->string('update_user')->comment('異動人員')->nullable();
            $table->dateTime('update_time')->comment('異動時間')->nullable();


            // 設定外鍵約束
            $table->foreign('role_id')->references('uuid')->on('role')->onDelete('cascade');
            $table->foreign('menu_id')->references('uuid')->on('sysmenu')->onDelete('cascade');

            // 避免同樣的 role-menu 兩次重複
            $table->unique(['role_id', 'menu_id']);
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_menus');
    }
};
