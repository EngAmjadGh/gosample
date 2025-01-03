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
        Schema::table('swap_requests', function (Blueprint $table) {
            $table->boolean('accepted_by_receiver')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('swap_requests', function (Blueprint $table) {
            $table->dropColumn('accepted_by_receiver');
        });
    }
};
