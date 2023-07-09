<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{

    public function WebNotification()
    {

        $notification_array['data'] = array();
        $notification_array['count'] = 0;

        $data = DB::table('tasks')
            ->select('tasks.task_name', 'tasks.task_next_date', 'tasks.task_notification_value', 'tasks.task_notification_type')
            ->where([
                'task_enabled' => true,
                'user_Id' => Auth::id()
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
                    array_push($notification_array['data'], [
                        'task_name' => $this->DecryptWithECC(Auth::user()->private_key, $item->task_name),
                        'task_next_date' => Carbon::createFromFormat('Y-m-d', $item->task_next_date)->format('d.m.Y'),
                    ]);
                    $notification_array['count']++;
                }
            }
        }

        return $notification_array;
    }
}
