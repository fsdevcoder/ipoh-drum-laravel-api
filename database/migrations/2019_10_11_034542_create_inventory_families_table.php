<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_families', function (Blueprint $table) { 
            $table->increments('id')->unique();
            $table->unsignedInteger('inventory_id')->unsigned();
            $table->string('uid')->unique();
            $table->string('code')->nullable();
            $table->string('sku')->nullable();
            $table->string('name');
            $table->string('desc')->nullable();
            $table->string('imgpublicid')->nullable()->unique();
            $table->string('imgpath')->nullable();
            $table->decimal('cost',8,2)->default(0.00);
            $table->decimal('price',8,2)->default(0.00);
            $table->integer('qty')->default(0);
            $table->integer('salesqty')->default(0);
            $table->boolean('onsale')->default(1);
            $table->boolean('status')->default(1);
            $table->timestamps();

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
        Schema::dropIfExists('inventory_families');
    }
}
