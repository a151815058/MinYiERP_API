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
        Schema::create('user', function (Blueprint $table) {
            $table->uuid('id')->comment('KEY')->primary()->unique();
            $table->string(column: 'useraccount')->comment('使用者帳號')->unique();
            $table->string('username')->comment('使用者名稱');
            $table->string('password_hash')->comment('密碼雜湊值');
            $table->string('mail')->comment('電子郵件');
            $table->string('remember_token')->comment('remember_token')->nullable();
            $table->string('is_valid')->comment('是否有效 0:失效 1:有效')->default(1);
            $table->string('create_user')->comment('建立人員')->default('admin');
            $table->dateTime('create_time')->comment('建立時間')->default(now());
            $table->string('update_user')->comment('異動人員')->nullable();
            $table->dateTime('update_time')->comment('異動時間')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
