<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('name')->unique();
            $table->string('email');
            $table->string('domain')->unique(); // tenant domain name
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('trial_period_end_date')->nullable();

            $table->string('db_server');
            $table->string('db_username');
            $table->string('db_password');

            $table->string('redis_server');

            $table->string('timezone');

            $table->integer('user_uid')->nullable();
            $table->integer('user_gid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenants');
    }
};
