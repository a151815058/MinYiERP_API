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
        Schema::create('invoiceinfo', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary()->unique();
            $table->string('invoice_type',255)->comment('發票類型');
            $table->string('series',255)->comment('序號');
            $table->string('period_start',6)->comment('期別_起');
            $table->string('period_end',6)->comment('期別_迄');
            $table->string('track_code',2)->comment('字軌代碼');
            $table->string('start_number',8)->comment('發票起始號碼');
            $table->string('end_number',8)->comment('發票截止號碼');
            $table->date('effective_startdate')->comment('適用起始日期');
            $table->date('effective_enddate')->comment('適用截止日期');
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
        Schema::dropIfExists('invoiceinfo');
    }
};
