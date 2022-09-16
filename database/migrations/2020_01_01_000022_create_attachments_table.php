<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->nullableUuidMorphs('attachable');
            $table->string('name')->nullable();
            $table->mediumText('path');
            $table->json('meta')->nullable();
            $table->string('group')->nullable();
            $table->string('mime')->nullable();
            $table->string('size')->nullable();
            $table->string('folder')->nullable();
            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
