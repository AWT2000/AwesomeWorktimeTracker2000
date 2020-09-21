<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('founder_id')->unsigned();
            $table->bigInteger('project_manager_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('founder_id')
                ->references('id')
                ->on('users');

            $table->foreign('project_manager_id')
                ->references('id')
                ->on('users');
        });

        Schema::table('worktime_entries', function (Blueprint $table) {
            $table->foreignId('project_id')
                ->nullable()
                ->after('user_id')
                ->references('id')
                ->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
