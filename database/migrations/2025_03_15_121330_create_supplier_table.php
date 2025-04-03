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
        Schema::create('supplier', function (Blueprint $table) {
            $table->uuid('Uuid')->primary();
            $table->string('supplierNo')->unique();
            $table->string('supplierShortNM');
            $table->string('supplierFullNM');
            $table->string('ZipCode1');
            $table->string('Address1');
            $table->string('ZipCode2')->nullable();
            $table->string('Address2')->nullable();
            $table->string('TaxID');
            $table->string('ResponsiblePerson');
            $table->dateTime('EstablishedDate');
            $table->string('Phone');
            $table->string('Fax');
            $table->string('ContactPerson');
            $table->string('ContactPhone');
            $table->string('MobilePhone');
            $table->string('ContactEmail');
            $table->string('CurrencyID');
            $table->string('TaxType');
            $table->string('PaymentTermID');
            $table->string('UserID');
            $table->string('Note')->nullable();
            $table->boolean('IsValid')->default(1);
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
        Schema::dropIfExists('supplier');
    }
};
