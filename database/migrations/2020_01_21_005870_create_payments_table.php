<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid')->unique();
            $table->unsignedInteger('channel_sale_id')->nullable();
            $table->unsignedInteger('sale_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->text('desc')->nullable();
            $table->string('type')->nullable();
            $table->string('method')->nullable();
            $table->string('reference')->nullable();
            $table->string('email');
            $table->string('contact')->nullable();
            $table->decimal('amount', 8, 2)->default(0.00);
            $table->decimal('charge', 8, 2)->default(0.00);
            $table->decimal('net', 8, 2)->default(0.00);
            $table->text('remark')->nullable();
            $table->string('saletype')->default("sale");
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('sale_id')
            ->references('id')
            ->on('sales')
            ->onUpdate('cascade')
            ->onDelete('restrict');
            
            $table->foreign('channel_sale_id')
            ->references('id')
            ->on('channel_sales')
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
        Schema::dropIfExists('payments');
    }
}
