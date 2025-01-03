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
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('task_confirmation_timestamp')->nullable();
            $table->timestamp('from_location_confirmation_timestamp')->nullable();
            $table->timestamp('to_location_confirmation_timestamp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('task_confirmation_timestamp');
            $table->dropColumn('from_location_confirmation_timestamp');
            $table->dropColumn('to_location_confirmation_timestamp');
        });
    }
};
