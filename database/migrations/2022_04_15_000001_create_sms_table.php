<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider_id')->nullable();
            $table->string('to');
            $table->uuid('patient_id')->nullable();
            $table->string('from');
            $table->text('message');
            $table->string('direction')->nullable();
            $table->boolean('scheduled')->nullable()->default(false);
            $table->datetime('scheduled_at')->nullable();
            $table->uuid('created_by')->index();
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms');
    }
}
