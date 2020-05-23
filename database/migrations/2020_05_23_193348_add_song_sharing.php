<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSongSharing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->enum('sharing_mode', ['off', 'single', 'group'])->default('off');
            $table->string('sharing_key')->nullable();
        });

        Schema::create('song_user', function (Blueprint $table) {
             $table->bigIncrements('id');
             $table->unsignedBigInteger('song_id');
             $table->unsignedBigInteger('user_id');
             $table->timestamps();

             $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
             $table->foreign('song_id')->references('id')->on('songs')->onDelete('cascade');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn('sharing_mode');
            $table->dropColumn('sharing_key');
        });

        Schema::dropIfExists('song_user');
    }
}
