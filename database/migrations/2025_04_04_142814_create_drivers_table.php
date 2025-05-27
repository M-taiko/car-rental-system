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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('id_number')->unique();
            $table->enum('id_type', ['national_id', 'iqama', 'passport']);
            $table->string('license_number')->unique();
            $table->date('license_expiry');
            $table->decimal('daily_rate', 10, 2);
            $table->text('address');
            $table->text('notes')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'assigned', 'off_duty'])->default('available');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
