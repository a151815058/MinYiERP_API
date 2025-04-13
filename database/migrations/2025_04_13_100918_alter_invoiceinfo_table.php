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
        Schema::table('invoiceinfo', function (Blueprint $table) {
            $table->string('invoice_type')->comment('發票類型');; 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoiceinfo', function (Blueprint $table) {
            //
        });
    }
};
