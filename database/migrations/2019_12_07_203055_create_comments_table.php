<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            
            $table->increments('id');
            $table->unsignedInteger('user_id')->unsigned()->nullable();
            $table->unsignedInteger('video_id')->unsigned()->nullable();
            $table->unsignedInteger('article_id')->unsigned()->nullable();
            $table->unsignedInteger('article_image_id')->unsigned()->nullable();
            $table->string('uid')->unique();
            $table->string('text');
            $table->string('imgpath')->nullable();
            $table->string('imgpublicid')->nullable();
            $table->integer('like')->default(0);
            $table->integer('dislike')->default(0);
            $table->string('type')->default('video');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('video_id')
            ->references('id')
            ->on('videos')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('article_id')
            ->references('id')
            ->on('articles')
            ->onUpdate('cascade')
            ->onDelete('restrict');
            
            $table->foreign('article_image_id')
            ->references('id')
            ->on('article_images')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('restrict');
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
