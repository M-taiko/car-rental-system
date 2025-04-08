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
        Schema::create('maintenance_parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenance_id'); // التأكد من أن النوع unsignedBigInteger
            $table->string('part_name');
            $table->decimal('part_cost', 8, 2);
            $table->timestamps();

            // إضافة الـ Foreign Key
            $table->foreign('maintenance_id')
                  ->references('id')
                  ->on('maintenance')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_parts');
    }
};
