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
        Schema::create('Product', function (Blueprint $table) {
            $table->uuid('Uuid')->primary();
            $table->string('ProductNO')->unique();
            $table->string('ProductNM');
            $table->string('Specification');
            $table->string('Barcode')->nullable();
            $table->decimal('Price_1');
            $table->decimal('Price_2')->nullable();
            $table->decimal('Price_3')->nullable();
            $table->decimal('Cost_1');
            $table->decimal('Cost_2')->nullable();
            $table->decimal('Cost_3')->nullable();
            $table->boolean('Batch_control')->default(1);
            $table->integer('Valid_days');
            $table->date('Effective_date');
            $table->boolean('Stock_control')->default(1);
            $table->integer('Safety_stock');
            $table->date('Expiry_date');
            $table->string(column: 'Description');
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
        Schema::dropIfExists('Product');
    }
};
