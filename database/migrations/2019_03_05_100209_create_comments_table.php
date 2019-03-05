<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('flagged')->default(false);
            $table->text('description');
            $table->unsignedInteger('commentable_id');
            $table->string('commentable_type');
            $table->timestamps();
            $table->softDeletes();

        });

        Schema::table('comments', function($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
        });

        Schema::create('taggable', function (Blueprint $table) {
           $table->bigIncrements('tag_id');
           $table->unsignedInteger('taggable_id');
           $table->string('taggable_type'); 
        });

        Schema::create('commentables', function (Blueprint $table) {
           $table->bigIncrements('comment_id');
           $table->unsignedInteger('commentable_id');
           $table->string('commentable_type'); 
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
