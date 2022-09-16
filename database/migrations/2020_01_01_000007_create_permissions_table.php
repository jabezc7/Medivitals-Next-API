<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->uuid('type_id')->nullable();
            $table->foreign('type_id')->references('id')->on('types');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('route');
            $table->boolean('active')->default(1);
            $table->boolean('hidden')->default(false);
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('permissions');
    }
}
