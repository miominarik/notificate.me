<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
Home Page Routes
*/

Route::get('/', function () {
    return view('home.index');
});

Route::get('/gdpr', function () {
    return view('home.index', [
        'page' => 'gdpr'
    ]);
})->name('gdpr');

Route::get('/cookies', function () {
    return view('home.index', [
        'page' => 'cookies'
    ]);
})->name('cookies');

Route::post('/contact/mail/send', "App\Http\Controllers\HomeController@SendContactMail")->name('contact.send_mail');


/*
Main App Routes
 */

Route::get('/app', function () {
    if (Auth::check()) {
        return redirect('/tasks');
    } else {
        return redirect('/login');
    };
});

Auth::routes(['verify' => true]);

Route::middleware(['auth', 'verified', 'blockedstatus', 'auth.session'])->group(function () {
    Route::get('tasks/{task_id?}', 'App\Http\Controllers\TasksController@index')->name('tasks.index');
    Route::post('tasks', 'App\Http\Controllers\TasksController@store')->name('tasks.store');
    Route::post('tasks/{task}/edit', 'App\Http\Controllers\TasksController@edit')->name('tasks.edit');
    Route::post('tasks/{task}/history', 'App\Http\Controllers\TasksController@ShowHistory')->name('tasks.history');
    Route::put('tasks/{task}', 'App\Http\Controllers\TasksController@update')->name('tasks.update');
    Route::delete('tasks/{task}', 'App\Http\Controllers\TasksController@destroy')->name('tasks.destroy');
    Route::put('tasks/complete/{task}', "App\Http\Controllers\TasksController@complete")->name('tasks.complete');
    Route::get('settings', "App\Http\Controllers\SettingsController@index")->name('settings.index');
    Route::put('settings/update', "App\Http\Controllers\SettingsController@update")->name('settings.update');
    Route::post('settings/change_password', "App\Http\Controllers\SettingsController@change_password")->name('settings.change_password');

    //Calendar
    Route::middleware('check_module:module_calendar')->group(function () {
        Route::get('calendar', "App\Http\Controllers\CalendarController@index")->name('calendar.index');
        Route::get('calendar/data_feed', "App\Http\Controllers\CalendarController@data_feed")->name('calendar.data_feed');
        Route::post('calendar/update_task_time/{task_id}', "App\Http\Controllers\CalendarController@update_task_time")->name('calendar.update_task_time');
    });

    //Modules
    Route::get('modules', "App\Http\Controllers\ModulesController@index")->name('modules.index');
    Route::get('modules/activate/{module_name}', "App\Http\Controllers\ModulesController@activate_modul")->name('modules.activate_modul');
    Route::get('modules/deactivate/{module_name}', "App\Http\Controllers\ModulesController@deactivate_modul")->name('modules.deactivate_modul');
});

//superadmin routes
Route::middleware(['auth', 'verified', 'superadmin'])->group(function () {
    Route::get('superadmin', function () {
        return redirect(route('superadmin.users'));
    })->name('superadmin.index');
    Route::get('superadmin/users', "App\Http\Controllers\SuperAdminController@all_users")->name('superadmin.users');
    Route::get('superadmin/users_modules', "App\Http\Controllers\SuperAdminController@users_modules")->name('superadmin.users_modules');
    Route::get('superadmin/users/{user_id}/detail', "App\Http\Controllers\SuperAdminController@user_detail")->name('superadmin.user_detail');
    Route::post('superadmin/users/{user_id}/update', "App\Http\Controllers\SuperAdminController@update_users_detail")->name('superadmin.update_users_detail');
    Route::get('superadmin/users/{user_id}/{auth_type}/deauthorization', "App\Http\Controllers\SuperAdminController@users_deauthorization")->name('superadmin.users_deauthorization');
    Route::post('superadmin/users/{user_id}/tooglestatus', "App\Http\Controllers\SuperAdminController@tooglestatus")->name('superadmin.tooglestatus');
    Route::get('superadmin/logs', "App\Http\Controllers\SuperAdminController@logs")->name('superadmin.logs');


});


Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'sk'])) {
        app()->setLocale($locale);
        session()->put('locale', $locale);
    }
    return redirect()->back();
});

Route::get('/auth/oauth/apple', function () {
    return Socialite::driver('apple')->redirect();
})->name('oauth.apple-login');

Route::get('/auth/oauth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('oauth.google-login');

Route::get('/auth/oauth/microsoft', function () {
    return Socialite::driver('microsoft')->redirect();
})->name('oauth.microsoft-login');

Route::post('/auth/oauth/callback/apple', "App\Http\Controllers\Auth\OauthController@AppleOauth");
Route::get('/auth/oauth/callback/google', "App\Http\Controllers\Auth\OauthController@GoogleOauth");
Route::get('/auth/oauth/callback/microsoft', "App\Http\Controllers\Auth\OauthController@MicrosoftOauth");
Route::get('/fcm_register/{token}', "App\Http\Controllers\ApiController@FCM_Token");
