<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSongThumbnail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->string('thumbnail_url')->default('');
            $table->boolean('is_rendered')->default(false);
        });

        Schema::table('song_video', function (Blueprint $table) {
            $table->boolean('is_included')->default(true);
        });

        DB::statement('update songs set is_rendered = true;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn('thumbnail_url');
            $table->dropColumn('is_rendered');
        });

        Schema::table('song_video', function (Blueprint $table) {
            $table->dropColumn('is_included');
        });
    }
}
