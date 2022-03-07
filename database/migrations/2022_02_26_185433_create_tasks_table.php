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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('task_name', 50);
            $table->string('task_note', 50)->nullable();
            $table->date('task_next_date');
            $table->mediumInteger('task_repeat_value');
            $table->tinyInteger('task_repeat_type');
            $table->mediumInteger('task_notification_value');
            $table->tinyInteger('task_notification_type');
            $table->boolean('task_enabled')->default(true);
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
        Schema::dropIfExists('tasks');
    }
};
