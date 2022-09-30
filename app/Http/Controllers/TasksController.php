<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteTaskRequest;
use App\Http\Requests\EditTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TasksController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($task_id = null)
    {
        if (isset($task_id) && !empty($task_id)) {

            $task_id = $this->JWT_decode($task_id);
            $task_id = $task_id[0];

            $task_pick = DB::table('tasks')
                ->select('id', 'task_name', 'task_note', 'task_next_date', 'task_repeat_value', 'task_repeat_type')
                ->where('user_id', Auth::id())
                ->where('task_enabled', true)
                ->where('id', $task_id)
                ->paginate(10);
        } else {
            $task_pick = DB::table('tasks')
                ->select('id', 'task_name', 'task_note', 'task_next_date', 'task_repeat_value', 'task_repeat_type')
                ->where('user_id', Auth::id())
                ->where('task_enabled', true)
                ->orderBy('task_next_date', 'ASC')
                ->paginate(10);
        };

        return view('tasks.Index', [
            'all_enabled_tasks' => $task_pick
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {

        // Retrieve the validated input data.
        $validated = $request->validated();

        if ($validated['task_next_date'] > Carbon::now()) {

            if ($validated['task_type'] == 0 || $validated['task_type'] == 1) {

                if ($validated['task_type'] == 0) {
                    $validated['task_repeat_value'] = NULL;
                    $validated['task_repeat_type'] = NULL;
                };

                $task = DB::table('tasks')->insertGetId([
                    'user_id' => Auth::id(),
                    'task_name' => $validated['task_name'],
                    'task_note' => $validated['task_note'],
                    'task_type' => $validated['task_type'],
                    'task_next_date' => $validated['task_next_date'],
                    'task_repeat_value' => $validated['task_repeat_value'],
                    'task_repeat_type' => $validated['task_repeat_type'],
                    'task_notification_value' => $validated['task_notification_value'],
                    'task_notification_type' => $validated['task_notification_type'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                $this->add_log('Add new task', $request->ip(), $task);

                return redirect(route('tasks.index'))->with('status_success', __('alerts.task_added'));
            }
        } else {
            return redirect(route('tasks.index'))->with('status_warning', __('alerts.task_added_wrong'));
        };
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function edit($task)
    {
        $return_data = DB::table('tasks')
            ->select('id', 'task_name', 'task_note', 'task_next_date', 'task_repeat_value', 'task_repeat_type', 'task_notification_value', 'task_notification_type', 'task_type')
            ->where('user_id', Auth::id())
            ->where('task_enabled', true)
            ->where('id', $task)
            ->get();

        return response()->json($return_data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(EditTaskRequest $request, $task)
    {
        // Retrieve the validated input data.
        $validated = $request->validated();

        if (is_null($validated['task_repeat_value'])) {
            $update_arr = [
                'task_name' => $validated['task_name'],
                'task_note' => $validated['task_note'],
                'task_repeat_value' => NULL,
                'task_repeat_type' => NULL,
                'task_notification_value' => $validated['task_notification_value'],
                'task_notification_type' => $validated['task_notification_type'],
                'task_next_date' => $validated['task_next_date_edit'],
                'updated_at' => Carbon::now()
            ];
        } else {
            $update_arr = [
                'task_name' => $validated['task_name'],
                'task_note' => $validated['task_note'],
                'task_repeat_value' => $validated['task_repeat_value'],
                'task_repeat_type' => $validated['task_repeat_type'],
                'task_notification_value' => $validated['task_notification_value'],
                'task_notification_type' => $validated['task_notification_type'],
                'task_next_date' => $validated['task_next_date_edit'],
                'updated_at' => Carbon::now()
            ];
        };

        DB::table('tasks')
            ->where('id', $task)
            ->where('user_id', Auth::id())
            ->where('task_enabled', true)
            ->update($update_arr);

        $this->add_log('Update task', $request->ip(), $task);

        return redirect(route('tasks.index'))->with('status_success', __('alerts.task_edited'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $task)
    {
        DB::table('tasks')
            ->where('id', $task)
            ->where('user_id', Auth::id())
            ->update([
                'task_enabled' => false
            ]);

        $this->add_log('Remove task', $request->ip(), $task);

        return redirect(route('tasks.index'))->with('status_success', __('alerts.task_removed'));
    }

    /**
     * Check the specified resource as complete
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function complete(CompleteTaskRequest $request, $task)
    {
        $validated = $request->validated();

        if (isset($validated['complete_date']) && !empty($validated['complete_date'])) {

            $next_data = DB::table('tasks')
                ->select('task_repeat_value', 'task_repeat_type')
                ->where([
                    'id' => $task,
                    'user_id' => Auth::id(),
                    'task_enabled' => true
                ])
                ->get();

            if (!empty($next_data[0])) {

                $date = Carbon::createFromFormat('Y-m-d', $validated['complete_date']);

                if ($next_data[0]->task_repeat_type == 1) { //Add x days
                    $new_date = $date->addDays($next_data[0]->task_repeat_value);
                } elseif ($next_data[0]->task_repeat_type == 2) { //Add x weeks
                    $new_date = $date->addWeeks($next_data[0]->task_repeat_value);
                } elseif ($next_data[0]->task_repeat_type == 3) { //Add x months
                    $new_date = $date->addMonths($next_data[0]->task_repeat_value);
                } elseif ($next_data[0]->task_repeat_type == 4) { //Add x months
                    $new_date = $date->addYears($next_data[0]->task_repeat_value);
                };

                $new_date = $date->format('Y-m-d');

                DB::table('tasks')
                    ->where([
                        'id' => $task,
                        'user_id' => Auth::id(),
                        'task_enabled' => true
                    ])
                    ->update([
                        'task_next_date' => $new_date,
                        'updated_at' => Carbon::now()
                    ]);

                $this->add_log('Complete task', $request->ip(), $task, Carbon::createFromFormat('Y-m-d', $validated['complete_date'])->format('Y-m-d H:i:s'));

                return redirect(route('tasks.index'))->with('status_success', __('alerts.task_completed'));
            }
        }
    }

    public function ShowHistory($task)
    {
        $return_data = DB::table('logs')
            ->select(DB::raw('DATE_FORMAT(created_at, "%d.%m.%Y") as created_at'))
            ->where([
                'user_id' => Auth::id(),
                'task_id' => $task,
                'log_type' => 'Complete task'
            ])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($return_data, 200);
    }
}
