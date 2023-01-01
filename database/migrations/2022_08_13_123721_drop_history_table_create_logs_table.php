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
        Schema::dropIfExists('history');

        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('log_type');
            $table->bigInteger('task_id')->nullable();
            $table->ipAddress();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
