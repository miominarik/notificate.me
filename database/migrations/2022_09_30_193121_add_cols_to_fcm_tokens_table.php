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
        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->string('device_name')->after('fcm_token')->nullable();
            $table->string('device_model')->after('device_name')->nullable();
            $table->string('android_id')->after('device_model')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->dropColumn(['device_name', 'device_model', 'android_id']);
        });
    }
};
