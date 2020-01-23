<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trailers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('video_id')->unsigned();
            $table->string('uid')->unique();
            $table->string('title');
            $table->string('desc')->nullable();
            $table->string('videopath');
            $table->string('videopublicid');
            $table->string('thumbnailpath')->nullable();
            $table->string('thumbnailpublicid')->nullable();
            $table->string('totallength');
            $table->integer('view')->default(0);
            $table->boolean('agerestrict')->default(false);
            $table->boolean('status')->default(true);
            $table->string('scope')->default('public');
            $table->timestamps();

            $table->foreign('video_id')
            ->references('id')
            ->on('videos')
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
        Schema::dropIfExists('trailers');
    }
}
