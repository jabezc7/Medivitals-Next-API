<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePermissionUserPivotTable extends Migration
{
    public function up()
    {
        Schema::create('permission_user', function (Blueprint $table) {
            $table->uuid('permission_id')->index();
            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->primary(['permission_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('permisssion_user');
    }
}
