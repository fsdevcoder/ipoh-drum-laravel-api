<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('back_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inventory_id')->unsigned()->nullable();
            $table->unsignedInteger('ticket_id')->unsigned()->nullable();
            $table->unsignedInteger('sale_id')->unsigned()->nullable();
            $table->string('uid')->unique();
            $table->integer('qty')->default(0);
            $table->text('desc')->nullable();
            $table->decimal('cost',8,2)->default(0.00);
            $table->decimal('price',8,2)->default(0.00);
            $table->decimal('totaldisc',8,2)->default(0.00);
            $table->decimal('linetotal',8,2)->default(0.00);
            $table->decimal('totalcost',8,2)->default(0.00);
            $table->decimal('grandtotal',8,2)->default(0.00);
            $table->string('type')->default('inventory');
            $table->date('docdate')->nullable();
            $table->string('status')->default('open');
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
        Schema::dropIfExists('back_orders');
    }
}
