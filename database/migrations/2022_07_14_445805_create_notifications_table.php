<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->uuid('patient_id')->index();
            $table->foreign('patient_id')->references('id')->on('users');
            $table->longText('message');
            $table->boolean('alert')->default(false);
            $table->string('priority')->nullable();
            $table->json('triggers')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
