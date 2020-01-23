<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('inventory_id')->unsigned()->nullable();
            $table->unsignedInteger('ticket_id')->unsigned()->nullable();
            $table->unsignedInteger('user_id')->unsigned()->nullable();
            $table->string('uid')->unique();
            $table->string('title');
            $table->string('desc');
            $table->string('imgpath')->nullable();
            $table->string('imgpublicid')->nullable()->unique();
            $table->string('type')->default('inventory');
            $table->decimal('rating',8,2)->default(0.00);
            $table->integer('like')->default(0);
            $table->integer('dislike')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('inventory_id')
            ->references('id')
            ->on('inventories')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('ticket_id')
            ->references('id')
            ->on('tickets')
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
        Schema::dropIfExists('product_reviews');
    }
}
