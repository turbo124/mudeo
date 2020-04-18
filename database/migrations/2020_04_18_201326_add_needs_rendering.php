<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNeedsRendering extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->boolean('needs_render')->default(false);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('platform')->nullable();
            $table->string('device')->nullable();
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
            $table->dropColumn('needs_render');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('platform');
            $table->dropColumn('device');
        });
    }
}
