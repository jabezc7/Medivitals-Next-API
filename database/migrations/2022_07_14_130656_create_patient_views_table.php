<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patient_views', function (Blueprint $table) {
            $table->uuid('patient_id')->index();
            $table->foreign('patient_id')->references('id')->on('users');
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->dateTime('last_viewed_at');
            $table->unique(['patient_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_views');
    }
};
