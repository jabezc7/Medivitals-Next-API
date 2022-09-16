<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->string('imei')->nullable();
            $table->string('number')->nullable();
            $table->string('nickname')->nullable();
            $table->json('frequencies')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
