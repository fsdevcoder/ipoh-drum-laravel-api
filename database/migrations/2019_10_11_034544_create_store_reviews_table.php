<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_reviews', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('store_id')->unsigned();
            $table->unsignedInteger('user_id')->unsigned();
            $table->string('uid')->unique();
            $table->string('title');
            $table->string('desc')->nullable();
            $table->string('imgpublicid')->nullable()->unique();
            $table->string('imgpath')->nullable();
            $table->decimal('rating',8,2)->default(0.00);
            $table->integer('like')->default(0);
            $table->integer('dislike')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('store_id')
            ->references('id')
            ->on('stores')
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
        Schema::dropIfExists('store_reviews');
    }
}
