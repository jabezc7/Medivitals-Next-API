<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypesTable extends Migration
{
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('group')->comment = 'Grouping Types Together';
            $table->string('container')->nullable()->comment = 'Grouping Groups';
            $table->string('abbreviation')->nullable();
            $table->longText('description')->nullable();
            $table->integer('value')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('locked')->default(false);
            $table->boolean('default')->default(false);
            $table->json('meta')->nullable();
            $table->integer('ordering')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('types');
    }
}
