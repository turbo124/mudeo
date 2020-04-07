<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYoutubePermId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->string('youtube_published_id')->nullable();
        });

        Schema::table('song_video', function (Blueprint $table) {
            $table->dropForeign('song_video_song_id_foreign');
            $table->dropForeign('song_video_video_id_foreign');

            $table->foreign('song_id')->references('id')->on('songs')->onDelete('cascade');
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
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
            $table->dropColumn('youtube_published_id');
        });
    }
}
