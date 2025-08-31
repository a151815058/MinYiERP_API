<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->uuid(column: 'id')->comment('KEY');
            $table->uuid('user_id')->comment('對應 users.uuid');
            $table->char('token_hash', 64)->comment('sha256');
            $table->timestamp('issued_at')->comment('發行時間');
            $table->timestamp('expires_at')->comment('過期時間');
            $table->timestamp('revoked_at')->nullable()->comment('撤銷時間');
            $table->string('device_info', 255)->nullable()->comment('裝置資訊');
            $table->string('ip_address', 45)->nullable()->comment('IP 位址');
            $table->unique(['user_id', 'token_hash'], 'uniq_user_token');
            $table->index(['expires_at', 'revoked_at'], 'idx_valid');
        });
    }

    public function down(): void {
        Schema::dropIfExists('refresh_tokens');
    }
};