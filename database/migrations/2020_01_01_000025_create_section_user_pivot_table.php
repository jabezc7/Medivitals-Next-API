<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSectionUserPivotTable extends Migration
{
    public function up()
    {
        Schema::create('section_user', function (Blueprint $table) {
            $table->uuid('section_id')->index();
            $table->foreign('section_id')->references('id')->on('sections');
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->primary(['section_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('section_user');
    }
}
