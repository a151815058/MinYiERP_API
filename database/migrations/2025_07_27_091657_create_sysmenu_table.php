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
        Schema::create('sysmenu', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary()->unique();
            $table->string('no_platform')->comment('作業平台'); 
            $table->string('no_prog')->comment('功能代碼'); 
            $table->string('nm_text')->comment('名稱'); 
            $table->string('gn_url')->comment('網站連結'); 
            $table->string('no_parent')->comment('父項')->nullable(); 
            $table->string('no_order')->comment('排序'); 
            $table->string('sc_kind')->comment('功能類型 新增/更新/刪除/查詢/列印/其他'); 
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
        Schema::dropIfExists('sysmenu');
    }
};
