<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_promotions', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('uid')->unique();
            $table->unsignedInteger('store_id')->unsigned()->nullable();
            $table->string('name');
            $table->text('desc')->nullable();
            $table->integer('qty')->default(0);
            $table->decimal('disc',8,2)->default(0.00);
            $table->integer('discpctg')->default(0);
            $table->boolean('discbyprice')->default(true);
            $table->date('promostartdate')->nullable();
            $table->date('promoenddate')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();


            $table->foreign('store_id')
            ->references('id')
            ->on('stores')
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
        Schema::dropIfExists('product_promotions');
    }
}
