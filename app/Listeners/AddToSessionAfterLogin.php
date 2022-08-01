<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AddToSessionAfterLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        //Uloženie práv do session pri boote

        $superadmin_get = DB::table('users')
            ->select('superadmin')
            ->where('id', Auth::id())
            ->get();

        if (isset($superadmin_get[0]->superadmin)) {

            if ($superadmin_get[0]->superadmin == 0 || $superadmin_get[0]->superadmin == 1) {
                switch ($superadmin_get[0]->superadmin) {
                    case 0:
                        Session::put('user_superadmin', 0);
                        break;
                    case 1:
                        Session::put('user_superadmin', 1);
                        break;
                    default:
                        Session::put('user_superadmin', 0);
                        break;
                };
            } else {
                Session::put('user_superadmin', 0);
            };
        } else {
            Session::put('user_superadmin', 0);
        };
    }
}
