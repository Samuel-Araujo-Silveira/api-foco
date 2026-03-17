<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_reservation_room', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_room_id');
            $table->unsignedBigInteger('rate_id');
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->foreign('reservation_room_id')
                  ->references('id')
                  ->on('reservation_rooms');

            $table->foreign('rate_id')
                  ->references('id')
                  ->on('rates');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_reservation_room');
    }
};