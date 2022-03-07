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
                    ->select('tasks.task_name', 'tasks.task_next_date', 'tasks.task_notification_value', 'tasks.task_notification_type', 'users.email', 'users.name')
                    ->where([
                        'task_enabled' => true
                    ])
                    ->get();

                if (!empty($data)) {
                    foreach ($data as $item) {

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

                if (!empty($notification_array)) {
                    foreach ($notification_array as $one_task) {
                        Mail::to($one_task['user_email'], $one_task['user_name'])->send(new NotificationMail($one_task['task_name'], $one_task['task_next_date']));
                    }
                }
            }else{
                return response()->json(['error' => 'API Token is invalid'], 200);
            };
        }else{
            return response()->json(['error' => 'Missing API Token'], 200);
        }
    }
}
