<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/tasks');
    } else {
        return redirect('/login');
    };
});

Auth::routes(['verify' => true]);

Route::resource('tasks', "App\Http\Controllers\TasksController")->except('show')->middleware(['auth','verified']);

Route::get('settings', "App\Http\Controllers\SettingsController@index")->name('settings.index');
Route::put('settings/update', "App\Http\Controllers\SettingsController@update")->name('settings.update');

Route::put('tasks/complete/{task}', "App\Http\Controllers\TasksController@complete")->name('tasks.complete')->middleware(['auth','verified']);


Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'sk'])) {
        app()->setLocale($locale);
        session()->put('locale', $locale);
    }
    return redirect()->back();
});
