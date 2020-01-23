<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('company_id')->unsigned()->nullable();
            $table->unsignedInteger('user_id')->unsigned()->nullable();
            $table->string('uid')->unique();
            $table->string('name');
            $table->string('contact');
            $table->longText('desc');
            $table->string('imgpath')->nullable();
            $table->string('imgpublicid')->nullable();
            $table->string('email');
            $table->decimal('rating',8,2)->default(0.00);
            $table->decimal('freeshippingminpurchase',8,2)->default(0.00);
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('companyBelongings')->default(true);
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('company_id')
            ->references('id')
            ->on('companies')
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
        Schema::dropIfExists('stores');
    }
}
