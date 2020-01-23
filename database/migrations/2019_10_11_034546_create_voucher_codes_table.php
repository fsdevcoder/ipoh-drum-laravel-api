<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('voucher_id')->unsigned();
            $table->unsignedInteger('user_id')->unsigned();
            $table->string('uid')->unique();
            $table->string('code');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('voucher_id')
            ->references('id')
            ->on('vouchers')
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
        Schema::dropIfExists('voucher_codes');
    }
}
