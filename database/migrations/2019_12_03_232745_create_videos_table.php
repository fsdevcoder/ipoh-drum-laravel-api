<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('channel_id')->unsigned();
            $table->unsignedInteger('playlist_id')->unsigned()->nullable();
            $table->string('uid')->unique();
            $table->string('title');
            $table->string('desc')->nullable();
            $table->string('videopath');
            $table->string('videopublicid');
            $table->string('imgpath')->nullable();
            $table->string('imgpublicid')->nullable();
            $table->string('totallength')->default(0);
            $table->integer('view')->default(0);
            $table->integer('like')->default(0);
            $table->integer('dislike')->default(0);
            $table->decimal('price',8,2)->default(0.00);
            $table->integer('discpctg')->default(0);
            $table->decimal('disc',8,2)->default(0.00);
            $table->boolean('discbyprice')->default(true);
            $table->boolean('free')->default(true);
            $table->integer('salesqty')->default(0);
            $table->string('scope')->default('public');
            $table->boolean('agerestrict')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('channel_id')
            ->references('id')
            ->on('channels')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('playlist_id')
            ->references('id')
            ->on('playlists')
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
        Schema::dropIfExists('videos');
    }
}
