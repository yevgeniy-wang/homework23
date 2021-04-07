<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('name')->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('label_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id');
            $table->foreignId('project_id');
            $table->timestamps();

            $table->foreign('label_id')->references('id')->on('labels');
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('label_project');
        Schema::dropIfExists('labels');
    }
}
