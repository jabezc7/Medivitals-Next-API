<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smsables', function (Blueprint $table) {
            $table->uuid('sms_id')->nullable();
            $table->foreign('sms_id')->references('id')->on('sms');
            $table->uuidMorphs('smsable');
            $table->unique(['smsable_id', 'sms_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smsables');
    }
}
