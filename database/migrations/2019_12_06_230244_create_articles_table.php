<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blogger_id')->unsigned();
            $table->string('uid')->unique();
            $table->string('title');
            $table->string('desc')->nullable();
            $table->integer('view')->default(0);
            $table->integer('like')->default(0);
            $table->integer('dislike')->default(0);
            $table->string('scope')->default('public');
            $table->boolean('agerestrict')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();


            $table->foreign('blogger_id')
            ->references('id')
            ->on('bloggers')
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
        Schema::dropIfExists('articles');
    }
}
