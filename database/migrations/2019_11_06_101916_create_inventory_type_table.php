<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_type', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('type_id')->unsigned();
            $table->unsignedInteger('inventory_id')->unsigned();
            $table->text('remark')->nullable();

            $table->foreign('type_id')
            ->references('id')
            ->on('types')
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
        Schema::dropIfExists('inventory_type');
    }
}
