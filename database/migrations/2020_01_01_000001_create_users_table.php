<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->string('first')->nullable();
            $table->string('last')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('position')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->boolean('super_admin')->default(false);
            $table->string('living_status')->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('suburb')->nullable();
            $table->string('postcode')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('medicare_number')->nullable();
            $table->date('medicare_expiry')->nullable();
            $table->string('medicare_position')->nullable();
            $table->string('private_health_fund')->nullable();
            $table->string('private_health_membership_no')->nullable();
            $table->string('gp_medical_centre')->nullable();
            $table->string('gp_name')->nullable();
            $table->string('gp_phone')->nullable();
            $table->string('gp_email')->nullable();
            $table->uuid('assignee_id')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('login_count')->default(0);
            $table->dateTime('last_login')->nullable();
            $table->uuid('created_by')->nullable();
            $table->string('timezone')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['email', 'deleted_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
    }
}
