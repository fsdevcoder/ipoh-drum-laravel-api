<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->unsigned();
            $table->unsignedInteger('inventory_id')->unsigned();
            $table->text('remark')->nullable();

            $table->foreign('category_id')
            ->references('id')
            ->on('categories')
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
        Schema::dropIfExists('category_inventory');
    }
}
