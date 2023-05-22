<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->float('miles');
            $table->unsignedBigInteger('car_id');
            $table->timestamps();

            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['car_id']);
        });

        Schema::dropIfExists('trips');
    }
}
