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
        Schema::create('billinfo', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary()->unique();
            $table->string('bill_no')->comment('單據代號')->unique();
            $table->string('bill_nm')->comment('單據名稱');
            $table->string('bill_type')->comment('單據類型');
            $table->string('bill_encode')->comment('單據編碼方式 1:年月日+3碼流水號 2:手動編碼');
            $table->integer('bill_calc')->comment('單據計算方式 1:單身單筆 2:整張計算');
            $table->integer('auto_review')->comment('是否自動核准 1:是 0:否');
            $table->string('gen_order')->comment('自動產生單據 1:自動 2:手動')->nullable();
            $table->string('gen_bill_type')->comment('產生單據類型[參數檔]')->nullable();
            $table->string('order_type')->comment('依照gen_bill_type動態產生欄位名稱')->nullable();
            $table->string('note')->comment('備註')->nullable();
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
        Schema::dropIfExists('billinfo');
    }
};
