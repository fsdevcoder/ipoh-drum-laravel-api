<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('store_id')->unsigned();
            $table->unsignedInteger('product_promotion_id')->unsigned()->nullable();
            $table->unsignedInteger('shipping_id')->unsigned()->nullable();
            $table->unsignedInteger('warranty_id')->unsigned()->nullable();
            $table->string('uid')->unique();
            $table->string('code')->nullable();
            $table->string('sku')->nullable();
            $table->string('name');
            $table->string('imgpublicid')->nullable()->unique();
            $table->string('imgpath')->nullable();
            $table->longText('desc')->nullable();
            $table->decimal('rating',8,2)->default(0.00);
            $table->decimal('cost',8,2)->default(0.00);
            $table->decimal('price',8,2)->default(0.00);
            $table->integer('qty')->default(0);
            $table->integer('promoendqty')->default(0);
            $table->integer('salesqty')->default(0);
            $table->integer('stockthreshold')->default(0);
            $table->boolean('status')->default(1);
            $table->boolean('onsale')->default(1);
            $table->timestamps();

            $table->foreign('store_id')
            ->references('id')
            ->on('stores')
            ->onUpdate('cascade')
            ->onDelete('restrict');
            
            $table->foreign('product_promotion_id')
            ->references('id')
            ->on('product_promotions')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('warranty_id')
            ->references('id')
            ->on('warranties')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('shipping_id')
            ->references('id')
            ->on('shippings')
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
        Schema::dropIfExists('inventories');
    }
}
