<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('user_id')->unsigned()->nullable();
            $table->unsignedInteger('store_id')->unsigned()->nullable();
            $table->unsignedInteger('voucher_id')->unsigned()->nullable();
            $table->string('uid')->unique();
            $table->string('sono')->nullable();
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->integer('qty')->default(0);
            $table->decimal('disc',8,2)->default(0.00);
            $table->decimal('totalcost',8,2)->default(0.00);
            $table->decimal('totalprice',8,2)->default(0.00);
            $table->decimal('charge',8,2)->default(0.00);
            $table->decimal('net',8,2)->default(0.00);
            $table->decimal('grandtotal',8,2)->default(0.00);
            $table->string('salestatus')->default('received');
            $table->boolean('status')->default(true);
            $table->text('remark')->nullable();
            $table->boolean('pos')->default(false);
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('store_id')
            ->references('id')
            ->on('stores')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('voucher_id')
            ->references('id')
            ->on('vouchers')
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
        Schema::dropIfExists('sales');
    }
}
