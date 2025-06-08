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
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('inventory_qty');
            $table->dropColumn('lot_num');
            $table->dropColumn('safety_stock');
            $table->dropColumn('lastStock_receiptdate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->decimal('inventory_qty')->comment('庫存數量')->nullable();
            $table->decimal('lot_num')->comment('批號')->nullable();
            $table->decimal('safety_stock')->comment('安全庫存')->nullable();
            $table->decimal('lastStock_receiptdate')->comment('最近一次進貨日')->nullable();
        });
    }
};
