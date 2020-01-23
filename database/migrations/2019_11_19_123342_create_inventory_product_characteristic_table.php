<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryProductCharacteristicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_product_characteristic', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('characteristic_id')->unsigned();
            $table->unsignedInteger('inventory_id')->unsigned();
            $table->text('remark')->nullable();
            $table->boolean('status')->default(true);

            $table->foreign('characteristic_id')
            ->references('id')
            ->on('product_characteristics')
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
        Schema::dropIfExists('inventory_product_characteristic');
    }
}
