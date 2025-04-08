<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            // إضافة سعر الشراء
            $table->decimal('purchase_price', 8, 2)->after('quantity');
            // إعادة تسمية العمود price إلى selling_price

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            //
        });
    }
};
