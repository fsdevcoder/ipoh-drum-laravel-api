<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('company_type_id')->unsigned();
            $table->string('uid')->unique();
            $table->string('name');
            $table->string('imgpath')->nullable();
            $table->string('imgpublicid')->nullable();
            $table->string('regno')->nullable();
            $table->string('tel1')->unique()->nullable();
            $table->string('tel2')->unique()->nullable();
            $table->string('fax1')->unique()->nullable();
            $table->string('fax2')->unique()->nullable();
            $table->string('email1')->unique()->nullable();
            $table->string('email2')->unique()->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('company_type_id')
            ->references('id')
            ->on('company_types')
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
        Schema::dropIfExists('companies');
    }
}
