<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupUserPivotTable extends Migration
{
    public function up()
    {
        Schema::create('group_user', function (Blueprint $table) {
            $table->uuid('group_id')->index();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->primary(['group_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('group_user');
    }
}
