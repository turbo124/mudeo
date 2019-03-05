<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('description');
            $table->unsignedInteger('duration');
            $table->unsignedInteger('likes');
            $table->boolean('flagged')->default(false);
            $table->boolean('is_public')->default(false);
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('trackable_id');
            $table->string('trackable_type');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });

        Schema::create('trackables' function (Blueprint $table) {
           $table->bigIncrements('track_id');
           $table->unsignedInteger('trackable_id');
           $table->string('trackable_type'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}
