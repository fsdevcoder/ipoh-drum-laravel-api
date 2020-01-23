<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_sales', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('user_id')->unsigned();
            $table->unsignedInteger('channel_id')->unsigned();
            $table->unsignedInteger('video_id')->unsigned();
            $table->string('uid')->unique();
            $table->decimal('disc',8,2)->default(0.00);
            $table->decimal('totalprice',8,2)->default(0.00);
            $table->decimal('charge',8,2)->default(0.00);
            $table->decimal('net',8,2)->default(0.00);
            $table->decimal('grandtotal',8,2)->default(0.00);
            $table->boolean('status')->default(true);
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('channel_id')
            ->references('id')
            ->on('channels')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('video_id')
            ->references('id')
            ->on('videos')
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
        Schema::dropIfExists('channel_sales');
    }
}
