<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayloadsTable extends Migration
{
    public function up()
    {
        Schema::create('payloads', function (Blueprint $table) {
            $table->id();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payloads');
    }
}
