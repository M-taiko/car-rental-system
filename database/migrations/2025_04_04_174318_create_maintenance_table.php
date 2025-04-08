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
        Schema::create('maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bike_id')->nullable()->onDelete('cascade');
            $table->decimal('cost', 10, 2);
            $table->text('description')->nullable();
            $table->dateTime('date');
            $table->string('status')->default('pending'); // 'pending', 'completed'
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance');
    }
};
