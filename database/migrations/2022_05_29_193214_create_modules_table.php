<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->smallInteger('discount_percent')->default(0);
            $table->boolean('module_sms')->default(false);
            $table->timestamps();
        });

        $all_users = DB::table('users')
            ->select('id')
            ->orderBy('id', 'asc')
            ->get();

        if (!empty($all_users)) {
            foreach ($all_users as $one_user) {
                DB::table('modules')
                    ->insert([
                        'user_id' => $one_user->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }
};
