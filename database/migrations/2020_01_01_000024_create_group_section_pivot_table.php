<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupSectionPivotTable extends Migration
{
    public function up()
    {
        Schema::create('group_section', function (Blueprint $table) {
            $table->uuid('section_id')->index();
            $table->foreign('section_id')->references('id')->on('sections');
            $table->uuid('group_id')->index();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->primary(['group_id', 'section_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_section');
    }
}
