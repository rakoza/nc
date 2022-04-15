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
        Schema::table('users', function (Blueprint $table) {
            //fk
            $table->foreignId('role_id')->constrained();

            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('notes', 1024)->nullable();
            $table->boolean('is_active')->default(true);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');

            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('notes');
            $table->dropColumn('is_active');
        });
    }
};
