<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteTaskRequest;
use App\Http\Requests\EditTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index($task_id = NULL)
    {
        if (isset($task_id) && !empty($task_id)) {

            $task_id = $this->JWT_decode($task_id);
            $task_id = $task_id[0];

            $task_pick = DB::table('tasks')
                ->select('id', 'task_name', 'task_note', 'task_next_date', 'task_repeat_value', 'task_repeat_type', 'notification')
                ->where('user_id', Auth::id())
                ->where('task_enabled', TRUE)
                ->where('id', $task_id)
                ->paginate(10);
        } else {
            $task_pick = DB::table('tasks')
                ->select('id', 'task_name', 'task_note', 'task_next_date', 'task_repeat_value', 'task_repeat_type', 'notification')
                ->where('user_id', Auth::id())
                ->where('task_enabled', TRUE)
                ->whereNull('ics_source_id')
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
     *
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

                if (!isset($validated['notification_status']) || is_null($validated['notification_status']) || empty($validated['notification_status']) && $validated['notification_status'] != 1) {
                    $validated['notification_status'] = FALSE;
                } else {
                    $validated['notification_status'] = TRUE;
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
                    'notification' => $validated['notification_status'],
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
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($task)
    {
        $return_data = DB::table('tasks')
            ->select('id', 'task_name', 'task_note', 'task_next_date', 'task_repeat_value', 'task_repeat_type', 'task_notification_value', 'task_notification_type', 'task_type', 'notification')
            ->where('user_id', Auth::id())
            ->where('task_enabled', TRUE)
            ->where('id', $task)
            ->get();

        return response()->json($return_data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function update(EditTaskRequest $request, $task)
    {
        // Retrieve the validated input data.
        $validated = $request->validated();

        if (!isset($validated['notification_status']) || is_null($validated['notification_status']) || empty($validated['notification_status']) && $validated['notification_status'] != 1) {
            $validated['notification_status'] = FALSE;
        } else {
            $validated['notification_status'] = TRUE;
        };

        if (is_null($validated['task_repeat_value'])) {
            $update_arr = [
                'task_name' => $validated['task_name'],
                'task_note' => $validated['task_note'],
                'task_repeat_value' => NULL,
                'task_repeat_type' => NULL,
                'task_notification_value' => $validated['task_notification_value'],
                'task_notification_type' => $validated['task_notification_type'],
                'task_next_date' => $validated['task_next_date_edit'],
                'notification' => $validated['notification_status'],
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
                'notification' => $validated['notification_status'],
                'updated_at' => Carbon::now()
            ];
        };

        DB::table('tasks')
            ->where('id', $task)
            ->where('user_id', Auth::id())
            ->where('task_enabled', TRUE)
            ->update($update_arr);

        $this->add_log('Update task', $request->ip(), $task);

        return redirect(route('tasks.index'))->with('status_success', __('alerts.task_edited'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $task)
    {
        DB::table('tasks')
            ->where('id', $task)
            ->where('user_id', Auth::id())
            ->update([
                'task_enabled' => FALSE
            ]);

        $this->add_log('Remove task', $request->ip(), $task);

        return redirect(route('tasks.index'))->with('status_success', __('alerts.task_removed'));
    }

    /**
     * Check the specified resource as complete
     *
     * @param \App\Models\Task $task
     *
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
                    'task_enabled' => TRUE
                ])
                ->get();

            if (!empty($next_data[0])) {

                $date = Carbon::createFromFormat('Y-m-d', $validated['complete_date']);

                if ($next_data[0]->task_repeat_type == 1) { //Add x days
                    $new_date = $date->addDays($next_data[0]->task_repeat_value);
                } else if ($next_data[0]->task_repeat_type == 2) { //Add x weeks
                    $new_date = $date->addWeeks($next_data[0]->task_repeat_value);
                } else if ($next_data[0]->task_repeat_type == 3) { //Add x months
                    $new_date = $date->addMonths($next_data[0]->task_repeat_value);
                } else if ($next_data[0]->task_repeat_type == 4) { //Add x months
                    $new_date = $date->addYears($next_data[0]->task_repeat_value);
                };

                $new_date = $date->format('Y-m-d');

                DB::table('tasks')
                    ->where([
                        'id' => $task,
                        'user_id' => Auth::id(),
                        'task_enabled' => TRUE
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

    public function Upload_File(Request $request)
    {
        if ($request->hasFile('uploading_file')) {
            if ($request->file('uploading_file')->getSize() < 31457280) {
                if ($request->has('task_id')) {
                    $task_id = $request->input('task_id');
                    $path = $request->file('uploading_file')->store('clients_files');
                    $name = $request->file('uploading_file')->getClientOriginalName();
                    $size = $request->file('uploading_file')->getSize();

                    if (!empty($path) && !empty($name)) {
                        DB::table('files')
                            ->insert([
                                'task_id' => $task_id,
                                'user_id' => Auth::id(),
                                'file_name' => $name,
                                'file_url' => $path,
                                'file_size' => $size,
                                'created_at' => Carbon::now()
                            ]);
                        $task_id = $this->JWT_encode($task_id);
                        return redirect(route('tasks.index', $task_id));
                    } else {
                        return redirect(route('tasks.index'))->with('status_danger', __('tasks.files_upload_error'));
                    };
                } else {
                    return redirect(route('tasks.index'))->with('status_danger', __('tasks.files_upload_error'));
                };
            } else {
                return redirect(route('tasks.index'))->with('status_warning', __('tasks.files_upload_big'));
            };
        } else {
            return redirect(route('tasks.index'))->with('status_danger', __('tasks.files_upload_error'));
        };
    }

    public function Show_All_files($task_id)
    {
        if (isset($task_id) && !empty($task_id)) {
            $get_files = DB::table('files')
                ->select('id', 'file_name', 'file_url')
                ->where('task_id', '=', $task_id)
                ->where('user_id', '=', Auth::id())
                ->get();

            if (!empty($get_files)) {
                foreach ($get_files as $porc => $one_file) {
                    $get_files[$porc]->id = $this->JWT_encode($one_file->id);
                }
            };

            return response()->json($get_files, 200);
        }
    }

    public function Download_file($file_id)
    {
        $file_id = $this->JWT_decode($file_id);
        $verify_my_file = DB::table('files')
            ->select('file_url', 'file_name')
            ->where('id', '=', $file_id)
            ->where('user_id', '=', Auth::id())
            ->get();

        if (isset($verify_my_file[0])) {
            if (Storage::exists($verify_my_file[0]->file_url)) {
                return Storage::download($verify_my_file[0]->file_url, $verify_my_file[0]->file_name);
            }
        };
        return redirect()->back();
    }

    public function DeleteFile($file_id)
    {
        $file_id = $this->JWT_decode($file_id);
        $verify_my_file = DB::table('files')
            ->select('file_url', 'file_name')
            ->where('id', '=', $file_id)
            ->where('user_id', '=', Auth::id())
            ->get();

        if (isset($verify_my_file[0])) {
            if (Storage::exists($verify_my_file[0]->file_url)) {
                $status = Storage::delete($verify_my_file[0]->file_url);
                if ($status == TRUE) {
                    $status_update = DB::table('files')
                        ->where('id', '=', $file_id)
                        ->where('user_id', '=', Auth::id())
                        ->delete();
                    if ($status_update == TRUE) {
                        return redirect()->back()->with('status_success', __('alerts.files_delete_success'));
                    } else {
                        return redirect()->back()->with('status_danger', __('alerts.files_delete_danger'));
                    };
                } else {
                    return redirect()->back()->with('status_danger', __('alerts.files_delete_danger'));
                };
            } else {
                return redirect()->back()->with('status_warning', __('alerts.files_delete_warming'));
            };
        } else {
            return redirect()->back()->with('status_warning', __('alerts.files_delete_warming'));
        };
    }

    public function AllFiles(Request $request)
    {
        $all_files = DB::table('files')
            ->select('files.file_name', 'files.id', 'files.file_size', 'files.created_at', 'tasks.task_name')
            ->join('tasks', 'tasks.id', '=', 'files.task_id')
            ->where('files.user_id', '=', Auth::id())
            ->orderByDesc('files.created_at')
            ->paginate(15);

        if ($all_files->count() > 0) {
            foreach ($all_files as $idcko => $file) {
                $all_files[$idcko]->file_size = $this->formatBytes($file->file_size, 2);
                $all_files[$idcko]->id = $this->JWT_encode($file->id);
                $all_files[$idcko]->created_at = Carbon::parse($file->created_at)->format('d.m.Y');
            }
        }

        return view('tasks.files.Index', [
            'all_files' => $all_files
        ]);
    }
}
