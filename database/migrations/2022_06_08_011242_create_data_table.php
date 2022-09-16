<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataTable extends Migration
{
    public function up()
    {
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payload_id')->nullable();
            $table->foreign('payload_id')->references('id')->on('payloads');
            $table->uuid('device_id')->index()->nullable();
            $table->foreign('device_id')->references('id')->on('devices');
            $table->uuid('patient_id')->index();
            $table->foreign('patient_id')->references('id')->on('users');
            $table->string('type');
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('data');
    }
}
