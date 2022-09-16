<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use NifteeGroup\NifteeCore\Models\Section;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->string('key');
            $table->string('value')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
