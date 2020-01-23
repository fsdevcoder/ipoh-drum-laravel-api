<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_type', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('type_id')->unsigned();
            $table->unsignedInteger('ticket_id')->unsigned();
            $table->text('remark')->nullable();
            $table->boolean('status')->default(true);

            $table->foreign('type_id')
            ->references('id')
            ->on('types')
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
        Schema::dropIfExists('ticket_type');
    }
}
