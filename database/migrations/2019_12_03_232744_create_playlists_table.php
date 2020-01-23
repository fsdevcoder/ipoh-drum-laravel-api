<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('channel_id')->unsigned()->nullable();
            $table->string('uid')->unique();
            $table->string('name')->unique();
            $table->string('scope')->default('public');
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            $table->foreign('channel_id')
            ->references('id')
            ->on('channels')
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
        Schema::dropIfExists('playlists');
    }
}
