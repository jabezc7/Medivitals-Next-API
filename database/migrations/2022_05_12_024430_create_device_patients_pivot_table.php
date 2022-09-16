<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicePatientsPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_patient', function (Blueprint $table) {
            $table->uuid('device_id')->index();
            $table->foreign('device_id')->references('id')->on('devices');
            $table->uuid('patient_id')->index();
            $table->foreign('patient_id')->references('id')->on('users');
            $table->primary(['device_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_patient', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('device_patient');
        });
    }
};
