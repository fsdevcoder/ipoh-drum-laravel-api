<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_role_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->unsigned();
            $table->unsignedInteger('role_id')->unsigned();
            $table->unsignedInteger('user_id')->unsigned();
            $table->string('assigned_by')->nullable();
            $table->dateTime('assigned_at')->nullable();
            $table->string('unassigned_by')->nullable();
            $table->dateTime('unassigned_at')->nullable();
            $table->text('remark')->nullable();
            $table->boolean('status')->default(true);

            $table->foreign('company_id')
            ->references('id')
            ->on('companies')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('role_id')
            ->references('id')
            ->on('roles')
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
        Schema::dropIfExists('company_role_user');
    }
}
