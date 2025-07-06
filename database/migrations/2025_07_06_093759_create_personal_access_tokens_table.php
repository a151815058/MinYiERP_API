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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string(column: 'tokenable_type')->comment('tokenable_type')->unique();
            $table->string('tokenable_id',255)->comment('tokenable_id');
            $table->string('name')->comment('name');
            $table->string('token',255)->comment('token');
            $table->string('abilities')->comment('abilities')->nullable();
            $table->string('last_used_at')->comment('最後使用時間')->nullable();
            $table->string('expires_at')->comment('過期時間')->nullable();
            $table->dateTime('created_at')->comment('建立時間')->default(now());
            $table->string('updated_at')->comment('異動時間')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
