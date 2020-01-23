<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatternsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patterns', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('inventory_family_id')->unsigned();
            $table->string('uid')->unique();
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

            $table->foreign('inventory_family_id')
            ->references('id')
            ->on('inventory_families')
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
        Schema::dropIfExists('patterns');
    }
}
