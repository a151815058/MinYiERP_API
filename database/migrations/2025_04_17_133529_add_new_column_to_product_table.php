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
        Schema::table('product', callback: function (Blueprint $table) {
            $table->string('main_supplier')->nullable()->comment('主要供應商'); 
            $table->string('Accounting')->nullable()->comment('科目認列'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('main_supplier');
            $table->dropColumn('Accounting');
        });
    }
};
