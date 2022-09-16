<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutomationsTable extends Migration
{
    public function up()
    {
        Schema::create('automations', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->uuid('patient_id')->nullable()->index();
            $table->foreign('patient_id')->references('id')->on('users');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->json('triggers')->nullable();
            $table->json('actions')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('global')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('automations');
    }
}
