<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('ics_sources', function(Blueprint $table) {
            $table->id();
            $table->bigInteger("user_id");
            $table->string("name");
            $table->text("ics_url");
            $table->boolean("allow_notif")->default(FALSE);
            $table->timestamp("created_at");
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ics_sources');
    }
};
