<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->uuid('type_id')->nullable();
            $table->foreign('type_id')->references('id')->on('types');
            $table->string('name')->nullable();
            $table->string('view')->nullable();
            $table->string('path')->nullable();
            $table->longText('content');
            $table->boolean('active')->default(true);
            $table->boolean('quick_link')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('templates');
    }
}
