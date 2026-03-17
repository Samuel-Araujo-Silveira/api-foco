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
        Schema::create('guest_counts', function (Blueprint $table) {
            $table->id();
            $table->integer('count');
            $table->string('type');
            $table->timestamps();

            $table->unsignedBigInteger('reservation_room_id');
            $table->foreign('reservation_room_id')->references('id')->on('reservation_rooms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_counts');
    }
};
