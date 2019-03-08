<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('handle')->nullable();
            $table->string('profile_image')->default('');
            $table->string('profile_image_url')->default('');
            $table->string('header_image')->default('');
            $table->string('header_image_url')->default('');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('oauth_user_id')->nullable()->unique();
            $table->unsignedInteger('oauth_provider_id')->nullable()->unique();
            $table->string('server_name')->default('');
            $table->string('token')->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('songs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('title')->default('');
            $table->string('url')->default('');
            $table->string('description')->default('');
            $table->unsignedInteger('duration')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->boolean('is_flagged')->default(false);
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });

        Schema::create('tracks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->default('');
            $table->string('description')->default('');
            $table->string('url')->default('');
            $table->unsignedInteger('duration')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->boolean('is_flagged')->default(false);
            $table->boolean('is_public')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });


        Schema::create('song_track', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('track_id');
            $table->unsignedBigInteger('song_id');
            $table->unsignedBigInteger('order_id')->default(0);
            $table->unsignedBigInteger('volume')->default(5);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('song_id')->references('id')->on('songs');
            $table->foreign('track_id')->references('id')->on('tracks');

        });

        Schema::create('song_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('song_id');

            $table->boolean('is_flagged')->default(false);
            $table->text('description');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('song_id')->references('id')->on('songs')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

        });

        Schema::create('track_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('track_id');
            $table->boolean('is_flagged')->default(false);
            $table->text('description');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

        });


        Schema::create('tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->boolean('is_flagged')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('taggables', function (Blueprint $table) {
           $table->unsignedBigInteger('tag_id');
           $table->unsignedBigInteger('taggable_id');
           $table->string('taggable_type'); 
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
