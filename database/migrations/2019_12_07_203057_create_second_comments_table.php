<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecondCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('second_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unsigned()->nullable();
            $table->unsignedInteger('comment_id')->unsigned();
            $table->string('uid')->unique();
            $table->string('text');
            $table->string('imgpath')->nullable();
            $table->string('imgpublicid')->nullable();
            $table->integer('like')->default(0);
            $table->integer('dislike')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('comment_id')
            ->references('id')
            ->on('comments')
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
        Schema::dropIfExists('second_comments');
    }
}
