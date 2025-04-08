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
        Schema::table('maintenance_parts', function (Blueprint $table) {
            // إزالة الأعمدة القديمة
            $table->dropColumn(['part_name', 'part_cost']);
            // إضافة العمود الجديد لربط قطعة الغيار
            $table->unsignedBigInteger('spare_part_id')->after('maintenance_id');
            $table->integer('quantity')->after('spare_part_id');
            // إضافة الـ Foreign Key
            $table->foreign('spare_part_id')
                  ->references('id')
                  ->on('spare_parts')
                  ->onDelete('restrict');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('maintenance_parts', function (Blueprint $table) {
            $table->dropForeign(['spare_part_id']);
            $table->dropColumn(['spare_part_id', 'quantity']);
            $table->string('part_name');
            $table->decimal('part_cost', 8, 2);
        });
    }
};
