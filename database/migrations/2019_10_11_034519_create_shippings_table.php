<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shippings', function (Blueprint $table) {
            
            $table->increments('id');
            $table->unsignedInteger('store_id')->unsigned()->nullable();
            $table->string('uid')->unique();
            $table->string('name');
            $table->text('desc')->nullable();
            $table->decimal('price',8,2)->default(0.00);
            $table->decimal('maxweight',8,2)->default(0.00);
            $table->decimal('maxdimension',8,2)->default(0.00);
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
        Schema::dropIfExists('shippings');
    }
}
