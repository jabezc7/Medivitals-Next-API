<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupPermissionPivotTable extends Migration
{
    public function up()
    {
        Schema::create('group_permission', function (Blueprint $table) {
            $table->uuid('permission_id')->index();
            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->uuid('group_id')->index();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->primary(['permission_id', 'group_id']);
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('group_permission');
    }
}
