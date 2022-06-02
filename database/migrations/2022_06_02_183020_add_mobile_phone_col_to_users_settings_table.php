<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_settings', function (Blueprint $table) {
            $table->string('mobile_number', 20)->nullable()->after('notification_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_settings', function (Blueprint $table) {
            $table->dropColumn('mobile_number');
        });
    }
};
