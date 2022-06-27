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

        //Uloženie farby témy do session pri boote
        $user_data = DB::table('users_settings')
            ->select('color_palette')
            ->where('user_id', Auth::id())
            ->get();

        if (isset($user_data[0]->color_palette) && !empty($user_data[0]->color_palette)) {
            $color_palette = match ($user_data[0]->color_palette) {
                1 => 'primary',
                2 => 'success',
                3 => 'warning',
                4 => 'danger',
                5 => 'dark',
                6 => 'light',
                7 => 'secondary',
                default => 'primary'
            };

            $color_scheme = match ($user_data[0]->color_palette) {
                1 => 'navbar-dark',
                2 => 'navbar-dark',
                3 => 'navbar-dark',
                4 => 'navbar-dark',
                5 => 'navbar-dark',
                6 => 'navbar-light',
                7 => 'navbar-dark',
                default => 'navbar-dark'
            };
        } else {
            $color_palette = 'primary';
            $color_scheme = 'navbar-dark';
        };

        session([
            'color_palette' => isset($color_palette) ? $color_palette : 'primary',
            'color_scheme' => isset($color_scheme) ? $color_scheme : 'navbar-dark'
        ]);

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
