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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('restrict');
            $table->dateTime('start_time');
            $table->dateTime('expected_end_time');
            $table->dateTime('actual_end_time')->nullable();
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->decimal('driver_price_per_day', 10, 2)->nullable();
            $table->decimal('expected_amount', 10, 2);
            $table->decimal('actual_amount', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('route_id')->nullable()->constrained('routes')->onDelete('set null');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
