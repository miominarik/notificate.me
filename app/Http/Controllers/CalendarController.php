<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function data_feed(Request $request)
    {
        $DB_data = DB::table('tasks')
            ->select('id', 'task_name', 'task_note', 'task_next_date')
            ->where('user_id', Auth::id())
            ->where('task_enabled', true);

        if (isset($request->start) && !empty($request->start) && isset($request->end) && !empty($request->end)) {
            $odkedy = Carbon::createFromDate($request->start)->format('Y-m-d');
            $dokedy = Carbon::createFromDate($request->end)->format('Y-m-d');

            $DB_data->whereBetween('task_next_date', [$odkedy, $dokedy]);

        };

        $DB_data = $DB_data->get();

        $response_data = array();

        if (isset($DB_data)) {
            foreach ($DB_data as $one_data) {
                array_push($response_data, [
                    'id' => $this->JWT_encode($one_data->id),
                    'title' => $one_data->task_name,
                    'start' => $one_data->task_next_date,
                    'end' => $one_data->task_next_date,
                    'extendedProps' => [
                        'description' => $one_data->task_note
                    ],
                ]);
            }
        }
        return response()->json($response_data);
    }

    public function update_task_time($task_id, Request $request)
    {
        if (isset($task_id) && !empty($task_id) && isset($request) && !empty($request)) {

            $task_id = $this->JWT_decode($task_id);
            $task_id = $task_id[0];

            DB::table('tasks')
                ->where('id', $task_id)
                ->update([
                    'task_next_date' => Carbon::createFromDate($request->time)->format('Y-m-d'),
                    'updated_at' => Carbon::now()
                ]);

            return 1;
        }
    }


}
