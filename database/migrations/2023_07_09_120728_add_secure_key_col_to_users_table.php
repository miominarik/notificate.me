<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->string('private_key', 256)->after("superadmin")->nullable();
        });

        $users = DB::table('users')
            ->get();

        if ($users->count() > 0) {
            foreach ($users as $user) {
                $keyPair = sodium_crypto_box_keypair();
                $privateKey = sodium_crypto_box_secretkey($keyPair);
                $privateKey = base64_encode($privateKey);
                if ($privateKey) {
                    DB::table('users')
                        ->where('id', '=', $user->id)
                        ->update([
                            'private_key' => $privateKey
                        ]);

                    $tasks = DB::table('tasks')
                        ->where('user_id', '=', $user->id)
                        ->get();
                    if ($tasks->count() > 0) {
                        foreach ($tasks as $task) {
                            if (!empty($task->task_name) && !is_null($task->task_name)) {
                                $controller = new Controller();
                                $encrypte_name = $controller->EncryptWithECC($privateKey, $task->task_name);
                                DB::table('tasks')
                                    ->where('id', '=', $task->id)
                                    ->update([
                                        'task_name' => $encrypte_name,
                                    ]);
                            }

                            if (!empty($task->task_note) && !is_null($task->task_note)) {
                                $encrypte_note = $controller->EncryptWithECC($privateKey, $task->task_note);
                                DB::table('tasks')
                                    ->where('id', '=', $task->id)
                                    ->update([
                                        'task_note' => $encrypte_note
                                    ]);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('private_key');
        });
    }
};
