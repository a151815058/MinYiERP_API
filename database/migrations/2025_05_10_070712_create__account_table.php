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
        Schema::create('Account', function (Blueprint $table) {
            $table->uuid('uuid')->comment('KEY')->primary()->unique();
            $table->string('account_no',255)->comment('會計科目代碼');
            $table->string('account_name',255)->comment('會計科目名稱');
            $table->string('Puuid')->comment('上階科目代碼')->nullable();
            $table->string('tier',255)->comment('層');
            $table->string('alter_name',255)->comment('英文別名')->nullable();
            $table->string('dc',255)->comment('借貸(借:Debit/貸:Credit)')->nullable();
            $table->string('note',255)->comment('說明');
            $table->string('is_valid',255)->comment('是否有效 0:失效 1:有效')->default(1);
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
        Schema::dropIfExists('Account');
    }
};
