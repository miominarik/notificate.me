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
    public function index()
    {
        return view('tasks.Index', [
            'all_enabled_tasks' => DB::table('tasks')
                ->select('id', 'task_name', 'task_note', 'task_next_date', 'task_repeat_value', 'task_repeat_type')
                ->where('user_id', Auth::id())
                ->where('task_enabled', true)
                ->orderBy('task_next_date', 'ASC')
                ->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {

        // Retrieve the validated input data.
        $validated = $request->validated();

        if ($validated['task_next_date'] > Carbon::now()) {
            $task = DB::table('tasks')->insertGetId([
                'user_id' => Auth::id(),
                'task_name' => $validated['task_name'],
                'task_note' => $validated['task_note'],
                'task_next_date' => $validated['task_next_date'],
                'task_repeat_value' => $validated['task_repeat_value'],
                'task_repeat_type' => $validated['task_repeat_type'],
                'task_notification_value' => $validated['task_notification_value'],
                'task_notification_type' => $validated['task_notification_type'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $this->AddNewActionToHistory($task, 2);

            return redirect(route('tasks.index'))->with('status_success', 'Úloha bola pridaná');
        } else {
            return redirect(route('tasks.index'))->with('status_warning', 'Úloha nebola pridaná. Dátum bol zadaný do minulosti');
        };
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit($task)
    {
        $return_data = DB::table('tasks')
            ->select('id', 'task_name', 'task_note', 'task_next_date', 'task_repeat_value', 'task_repeat_type', 'task_notification_value', 'task_notification_type')
            ->where('user_id', Auth::id())
            ->where('task_enabled', true)
            ->where('id', $task)
            ->get();

        return response()->json($return_data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(EditTaskRequest $request, $task)
    {
        // Retrieve the validated input data.
        $validated = $request->validated();

        DB::table('tasks')
            ->where('id', $task)
            ->where('user_id', Auth::id())
            ->where('task_enabled', true)
            ->update([
                'task_name' => $validated['task_name'],
                'task_note' => $validated['task_note'],
                'task_repeat_value' => $validated['task_repeat_value'],
                'task_repeat_type' => $validated['task_repeat_type'],
                'task_notification_value' => $validated['task_notification_value'],
                'task_notification_type' => $validated['task_notification_type'],
                'updated_at' => Carbon::now()
            ]);

        $this->AddNewActionToHistory($task, 1);

        return redirect(route('tasks.index'))->with('status_success', 'Úloha bola upravená');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($task)
    {
        DB::table('tasks')
            ->where('id', $task)
            ->where('user_id', Auth::id())
            ->update([
                'task_enabled' => false
            ]);

        $this->AddNewActionToHistory($task, 3);

        return redirect(route('tasks.index'))->with('status_success', 'Úloha bola vymazaná');
    }

    /**
     * Check the specified resource as complete
     *
     * @param  \App\Models\Task  $task
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

                $this->AddNewCompleteToHistory($task, Carbon::createFromFormat('Y-m-d', $validated['complete_date'])->format('Y-m-d H:i:s'));
                return redirect(route('tasks.index'))->with('status_success', 'Úloha bola splnená. Dátum nasledujúceho splnenia bol posunutý.');
            }
        }
    }

    /**
     * AddNewActionToHistory
     *
     * @param  mixed $task_id
     * @param  mixed $user_id
     * @param  mixed $action
     * @param  mixed $date
     * @return void
     */
    public function AddNewActionToHistory($task_id, $action)
    {
        if (isset($task_id) && isset($action)) {
            DB::table('history')
                ->insert([
                    'task_id' => $task_id,
                    'user_id' => Auth::id(),
                    'log_type' => $action,
                    'created_at' => Carbon::now()
                ]);
        }
    }

    /**
     * AddNewCompleteToHistory
     *
     * @param  mixed $task_id
     * @param  mixed $user_id
     * @param  mixed $action
     * @param  mixed $date
     * @return void
     */
    public function AddNewCompleteToHistory($task_id, $date)
    {
        if (isset($task_id) && isset($date)) {
            DB::table('history')
                ->insert([
                    'task_id' => $task_id,
                    'user_id' => Auth::id(),
                    'log_type' => 99,
                    'created_at' => $date
                ]);
        }
    }

    public function ShowHistory($task)
    {
        $return_data = DB::table('history')
            ->select(DB::raw('DATE_FORMAT(created_at, "%d.%m.%Y") as created_at'))
            ->where([
                'user_id' => Auth::id(),
                'task_id' => $task,
                'log_type' => 99
            ])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($return_data, 200);
    }
}
