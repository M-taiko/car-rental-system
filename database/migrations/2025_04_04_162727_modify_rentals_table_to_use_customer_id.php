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
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->unsignedBigInteger('customer_id')->nullable()->after('bike_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
            $table->unsignedBigInteger('user_id')->nullable()->after('bike_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
