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
        Schema::table('productinventory', function (Blueprint $table) {
            $table->string('Restock_time')->nullable()->comment('最近一次進貨日'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productinventory', function (Blueprint $table) {
            $table->dropColumn('Restock_time');
        });
    }
};
