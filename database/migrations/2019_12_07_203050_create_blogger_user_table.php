<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBloggerUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogger_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blogger_id')->unsigned();
            $table->unsignedInteger('user_id')->unsigned();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('blogger_id')
            ->references('id')
            ->on('bloggers')
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
        Schema::dropIfExists('blogger_user');
    }
}
