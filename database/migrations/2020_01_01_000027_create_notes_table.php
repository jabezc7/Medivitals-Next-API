<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->nullableUuidMorphs('noteable');
            $table->uuid('parent_id')->nullable()->index();
            $table->uuid('created_by')->index();
            $table->foreign('created_by')->references('id')->on('users');
            $table->longText('note');
            $table->boolean('private')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notes');
    }
}
