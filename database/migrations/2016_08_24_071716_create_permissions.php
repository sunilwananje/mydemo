<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
        });
        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('mst_permission_users', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->foreign('permission_id')->references('id')->on('mst_permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['permission_id', 'user_id']);
        });
        
        Schema::table('users', function ($table) {
            $table->integer('role_id')->unsigned()->nullable();
            $table->foreign('role_id')->references('id')->on('mst_roles')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mst_permissions');
        Schema::drop('mst_permission_users');
    }
}
