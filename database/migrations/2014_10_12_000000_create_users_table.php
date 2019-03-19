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
            $table->string('name')->default('');
            $table->string('handle')->nullable();
            $table->string('profile_image_url')->default('');
            $table->string('header_image_url')->default('');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('oauth_user_id')->nullable()->unique();
            $table->unsignedInteger('oauth_provider_id')->nullable()->unique();
            $table->string('server_name')->default('');
            $table->string('ip')->default('');
            $table->string('facebook_social_url')->default('');
            $table->string('youtube_social_url')->default('');
            $table->string('instagram_social_url')->default('');
            $table->string('soundcloud_social_url')->default('');
            $table->string('twitch_social_url')->default('');
            $table->string('twitter_social_url')->default('');
            $table->string('website_social_url')->default('');
            $table->string('token')->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('songs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('genre_id')->default(1);
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->string('title')->default('');
            $table->string('url')->default('');
            $table->string('description')->default('');
            $table->unsignedInteger('duration')->default(0);
            $table->unsignedInteger('count_like')->default(0);
            $table->unsignedInteger('count_play')->default(0);
            $table->boolean('is_flagged')->default(false);
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });

        Schema::create('videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url')->default('');
            $table->string('thumbnail_url')->default('');
            $table->unsignedBigInteger('timestamp')->default(0);
            $table->boolean('is_flagged')->default(false);
            $table->boolean('is_public')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });


        Schema::create('song_video', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('video_id');
            $table->unsignedBigInteger('song_id');
            $table->unsignedBigInteger('order_id')->default(0);
            $table->unsignedBigInteger('volume')->default(5);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('song_id')->references('id')->on('songs');
            $table->foreign('video_id')->references('id')->on('videos');

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

        Schema::create('song_likes', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('song_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('song_id')->references('id')->on('songs')->onDelete('cascade');
        });

        Schema::create('video_likes', function (Blueprint $table){
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('video_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('users');
    }
}
