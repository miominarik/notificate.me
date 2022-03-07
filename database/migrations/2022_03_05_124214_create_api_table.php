<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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
        Schema::create('api', function (Blueprint $table) {
            $table->id();
            $table->string('api_token', 255);
            $table->string('note', 50)->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        DB::table('api')
            ->insert([
                'api_token' => Crypt::encryptString(rand()),
                'note' => 'System API',
                'created_at' => Carbon::now()
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api');
    }
};
