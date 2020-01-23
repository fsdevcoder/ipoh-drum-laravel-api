<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFeatureTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_feature_ticket', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_feature_id')->unsigned();
            $table->unsignedInteger('ticket_id')->unsigned();
            $table->text('remark')->nullable();
            $table->boolean('status')->default(true);

            $table->foreign('product_feature_id')
            ->references('id')
            ->on('product_features')
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
        Schema::dropIfExists('product_feature_ticket');
    }
}
