<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->uuid('type_id')->nullable();
            $table->foreign('type_id')->references('id')->on('types');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->boolean('active')->default(true);
            $table->integer('ordering')->nullable();
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('groups');
    }
}
