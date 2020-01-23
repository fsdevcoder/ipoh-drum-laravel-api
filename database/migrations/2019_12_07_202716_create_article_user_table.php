<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('article_id')->unsigned();
            $table->unsignedInteger('user_id')->unsigned();
            $table->string('review')->nullable();
            $table->boolean('saved')->default(false);
            $table->string('watchedlength')->default('0:00');
            $table->string('status')->default('clicked');
            $table->timestamps();

            $table->foreign('article_id')
            ->references('id')
            ->on('articles')
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
        Schema::dropIfExists('article_user');
    }
}
