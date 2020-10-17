<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotTableEntryCollisions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_collisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worktime_entry_one_id');
            $table->unsignedBigInteger('worktime_entry_two_id');
            $table->timestamps();

            $table->foreign('worktime_entry_one_id')
                ->references('id')
                ->on('worktime_entries')
                ->onDelete('cascade');

            $table->foreign('worktime_entry_two_id')
                ->references('id')
                ->on('worktime_entries')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_collisions');
    }
}
