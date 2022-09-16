<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->uuid('parent_id')->nullable()->index();
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->string('route')->nullable();
            $table->integer('ordering')->default(0);
            $table->integer('level')->default(1);
            $table->boolean('active')->default(true);
            $table->boolean('hidden')->default(false);
            $table->string('permission')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sections');
    }
}
