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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('client_type')->nullable()->comment('客戶型態'); 
            $table->string('account_category')->nullable()->comment('科目別'); 
            $table->string('invoice_title')->nullable()->comment('發票抬頭'); 
            $table->string('delivery_method')->nullable()->comment('發票寄送方式'); 
            $table->string('recipient_name')->nullable()->comment('發票收件人'); 
            $table->string('recipient_phone')->nullable()->comment('發票連絡電話'); 
            $table->string('recipient_email')->nullable()->comment('發票收件人信箱'); 
            $table->string('invoice_address')->nullable()->comment('發票地址'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('client_type');
            $table->dropColumn('account_category');
            $table->dropColumn('invoice_title');
            $table->dropColumn('delivery_method');
            $table->dropColumn('recipient_name');
            $table->dropColumn('recipient_phone');
            $table->dropColumn('recipient_email');
            $table->dropColumn('invoice_address');
        });
    }
};
