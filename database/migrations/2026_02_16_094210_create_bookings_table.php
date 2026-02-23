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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('bookingID');
            $table->unsignedBigInteger('customerID');
            $table->date('eventdate');
            $table->time('eventtime');
            $table->string('eventdetails');
            $table->string('status')->default('pending');
            $table->decimal('totalamount', 10, 2);
            $table->timestamps();

            $table->foreign('customerID')->references('customerID')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
