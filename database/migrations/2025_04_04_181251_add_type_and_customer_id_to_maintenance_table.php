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
        Schema::table('maintenance', function (Blueprint $table) {
            $table->enum('type', ['internal', 'customer'])->default('internal')->after('bike_id');
            $table->foreignId('customer_id')->nullable()->after('type')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('maintenance', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['type', 'customer_id']);
        });
    }
};
