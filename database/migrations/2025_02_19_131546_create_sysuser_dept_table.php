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
        Schema::create('sysuser_depts', function (Blueprint $table) {
            $table->id()->comment('KEY')->unique();;
            $table->uuid('dept_id')->comment('dept_id');
            $table->uuid('user_id')->comment('user_id');
            $table->boolean('is_valid')->comment('是否有效')->default(1);
            $table->string('create_user')->comment('建立人員')->default('admin');
            $table->dateTime('create_time')->comment('建立時間')->default(now());
            $table->string('update_user')->comment('異動人員')->nullable();
            $table->dateTime('update_time')->comment('異動時間')->nullable();

            // 設定外鍵約束
            $table->foreign('Dept_id')->references('uuid')->on('depts')->onDelete('cascade');
            $table->foreign('User_id')->references('uuid')->on('sysusers')->onDelete('cascade');

            // 避免同樣的 user-dept 兩次重複
            $table->unique(['Dept_id', 'User_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sysuser_depts');
    }
};
