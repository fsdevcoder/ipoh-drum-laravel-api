<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('sale_id')->unsigned()->nullable();
            $table->unsignedInteger('inventory_id')->unsigned()->nullable();
            $table->unsignedInteger('inventory_family_id')->unsigned()->nullable();
            $table->unsignedInteger('pattern_id')->unsigned()->nullable();
            $table->unsignedInteger('ticket_id')->unsigned()->nullable();
            $table->string('uid')->unique();
            $table->string('name');
            $table->string('trackingcode')->nullable();
            $table->integer('qty')->default(0);
            $table->text('desc')->nullable();
            $table->decimal('cost',8,2)->default(0.00);
            $table->decimal('price',8,2)->default(0.00);
            $table->decimal('disc',8,2)->default(0.00);
            $table->decimal('totalprice',8,2)->default(0.00);
            $table->decimal('totalcost',8,2)->default(0.00);
            $table->decimal('grandtotal',8,2)->default(0.00);
            $table->string('status')->default(true);
            $table->string('type')->default('inventory');
            $table->timestamps();

            $table->foreign('sale_id')
            ->references('id')
            ->on('sales')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('inventory_id')
            ->references('id')
            ->on('inventories')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('inventory_family_id')
            ->references('id')
            ->on('inventory_families')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('pattern_id')
            ->references('id')
            ->on('patterns')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('ticket_id')
            ->references('id')
            ->on('tickets')
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
        Schema::dropIfExists('sale_items');
    }
}
