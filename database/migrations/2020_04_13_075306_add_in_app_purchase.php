<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInAppPurchase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('users', function (Blueprint $table) {
            $table->string('order_id')->nullable();
            $table->string('order_expires')->nullable();
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->boolean('is_public')->change()->default(true);
        });

        DB::statement('update songs set is_public = 1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('order_id');
            $table->dropColumn('order_expires');
        });
    }
}
