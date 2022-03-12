<?php

namespace App\Http\Controllers;

use App\Mail\NotificationMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ApiController extends Controller
{

    /**
     * Checks all tasks and their completion dates. If the due date is less than the current one, it will send an e-mail.
     */
    protected function AgentCheckDates($token)
    {
        if (!empty($token)) {
            $token_verify = DB::table('api')
                ->where([
                    'api_token' => $token,
                    'enabled' => true
                ])
                ->count();

            if ($token_verify === 1) {
                $notification_array = array();

                $data = DB::table('tasks')
                    ->join('users', 'tasks.user_id', '=', 'users.id')
                    ->join('users_settings', 'tasks.user_id', '=', 'users_settings.user_id')
                    ->select('tasks.task_name', 'tasks.task_next_date', 'tasks.task_notification_value', 'tasks.task_notification_type', 'users.email', 'users.name', 'users_settings.enable_email_notification', 'users_settings.notification_time')
                    ->where([
                        'task_enabled' => true
                    ])
                    ->get();

                if (!empty($data)) {
                    foreach ($data as $item) {

                        //Checks if the user wants to send notifications
                        if ($item->enable_email_notification == 0) {
                            continue;
                        };

                        $checked_date = Carbon::createFromFormat('Y-m-d', $item->task_next_date);

                        if ($item->task_notification_type == 1) { //Add x days
                            $new_date = $checked_date->subDays($item->task_notification_value);
                        } elseif ($item->task_notification_type == 2) { //Add x weeks
                            $new_date = $checked_date->subWeeks($item->task_notification_value);
                        } elseif ($item->task_notification_type == 3) { //Add x months
                            $new_date = $checked_date->subMonths($item->task_notification_value);
                        } elseif ($item->task_notification_type == 4) { //Add x months
                            $new_date = $checked_date->subYears($item->task_notification_value);
                        };

                        $new_date = $new_date->format('Y-m-d');

                        //check whether the current time is the same as set by the user
                        $now = Carbon::now()->format('H');
                        $date2 = Carbon::createFromFormat('H:i:s', $item->notification_time)->format('H');

                        if ($now == $date2) {
                            if ($new_date <= Carbon::now()) {
                                array_push($notification_array, [
                                    'task_name' => $item->task_name,
                                    'task_next_date' => $item->task_next_date,
                                    'user_email' => $item->email,
                                    'user_name' => $item->name
                                ]);
                            }
                        }
                    }
                }

                if (!empty($notification_array)) {
                    foreach ($notification_array as $one_task) {
                        Mail::to($one_task['user_email'], $one_task['user_name'])->send(new NotificationMail($one_task['task_name'], $one_task['task_next_date']));
                    }
                }
            } else {
                return response()->json(['error' => 'API Token is invalid'], 200);
            };
        } else {
            return response()->json(['error' => 'Missing API Token'], 200);
        }
    }
}
