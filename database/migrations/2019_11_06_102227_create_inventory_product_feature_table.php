<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryProductFeatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_product_feature', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_feature_id')->unsigned();
            $table->unsignedInteger('inventory_id')->unsigned();
            $table->text('remark')->nullable();

            $table->foreign('product_feature_id')
            ->references('id')
            ->on('product_features')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('inventory_id')
            ->references('id')
            ->on('inventories')
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
        Schema::dropIfExists('inventory_product_feature');
    }
}
