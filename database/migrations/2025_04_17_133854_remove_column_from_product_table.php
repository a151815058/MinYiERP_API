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
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('price_2');
            $table->dropColumn('price_3');
            $table->dropColumn('cost_2');
            $table->dropColumn('cost_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->decimal('price_2')->comment('售價二')->nullable();
            $table->decimal('price_3')->comment('售價三')->nullable();
            $table->decimal('cost_2')->comment('進價二')->nullable();
            $table->decimal('cost_3')->comment('進價三')->nullable();
        });
    }
};
