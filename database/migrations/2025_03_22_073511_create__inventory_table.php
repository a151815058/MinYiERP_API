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
        Schema::create('Inventory', function (Blueprint $table) {
            $table->uuid('Uuid')->primary();
            $table->string('InventoryNO')->unique();
            $table->string('InventoryNM');
            $table->decimal('InventoryQty')->default(0);
            $table->string('LotNum')->nullable();
            $table->decimal('Safety_stock')->default(0);
            $table->date('LastStockReceiptDate')->nullable();
            $table->boolean('IsVaild')->default(1);
            $table->string('Createuser');
            $table->dateTime('CreateTime')->nullable();
            $table->string('UpdateUser');
            $table->dateTime('UpdateTime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Inventory');
    }
};
