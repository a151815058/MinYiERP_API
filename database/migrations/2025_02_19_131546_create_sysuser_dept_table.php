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
            $table->id()->unique();;
            $table->uuid('Dept_id');
            $table->uuid('User_id');
            $table->boolean('IsVaild')->default(1);
            $table->string('Createuser');
            $table->dateTime('CreateTime')->nullable();
            $table->string('UpdateUser');
            $table->dateTime('UpdateTime')->nullable();

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
