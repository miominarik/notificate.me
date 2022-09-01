<?php

use App\Http\Middleware\WhiteListIpAddressessMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/main_check_agent_email', "App\Http\Controllers\ApiController@AgentCheckDates")->middleware(WhiteListIpAddressessMiddleware::class);
Route::get('/main_check_agent_sms', "App\Http\Controllers\ApiController@AgentCheckDatesSMS")->middleware(WhiteListIpAddressessMiddleware::class);
Route::get('/unsubscribe/{user_email}', "App\Http\Controllers\SettingsController@Email_unsubscribe");
Route::get('/ics/public/{hash}', "App\Http\Controllers\CalendarController@GenerateICS");
