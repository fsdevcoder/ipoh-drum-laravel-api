<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->unsigned()->nullable();
            $table->unsignedInteger('user_id')->unsigned()->nullable();
            $table->string('uid')->unique();
            $table->string('name')->unique();
            $table->string('desc')->nullable();
            $table->string('email')->nullable();
            $table->string('imgpath')->nullable();
            $table->string('imgpublicid')->nullable();
            $table->string('tel1')->nullable();
            $table->boolean('companyBelongings')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('company_id')
            ->references('id')
            ->on('companies')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('user_id')
            ->references('id')
            ->on('users')
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
        Schema::dropIfExists('channels');
    }
}
