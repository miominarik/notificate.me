<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

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
            ->where('task_enabled', TRUE);

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

            $this->add_log('Update task time', $request->ip(), $task_id);

            return 1;
        }
    }

    public function GenerateICS(string $hash)
    {
        if (isset($hash) && !empty($hash)) {

            if (isset($hash) && !empty($hash)) {
                //Získanie eventov z DB

                $user_id = DB::table('users')
                    ->select('id')
                    ->where('email', '=', $hash)
                    ->get();

                if (isset($user_id[0])) {
                    $events = DB::table('tasks')
                        ->where('user_id', '=', $user_id[0]->id)
                        ->where('task_enabled', '=', 1)
                        ->get();

                    //Založenie icalu
                    if (isset($events) && !empty($events)) {
                        //Pridanie eventov
                        $array_events = array();
                        foreach ($events as $one_event) {
                            $event = Event::create()
                                ->name($one_event->task_name)
                                ->description(!empty($one_event->task_note) ? $one_event->task_note : '')
                                ->uniqueIdentifier('notificateme_' . $one_event->id)
                                ->createdAt(new \DateTime(Carbon::parse($one_event->created_at)->format('Y-m-d')))
                                ->startsAt(new \DateTime($one_event->task_next_date . '00:00'))
                                ->endsAt(new \DateTime($one_event->task_next_date . '23:59'))
                                ->fullDay();

                            if ($one_event->task_type == 1) {
                                $repeat_value = match ($one_event->task_repeat_type) {
                                    1 => 'daily',
                                    2 => 'weekly',
                                    3 => 'monthly',
                                    4 => 'yearly'
                                };
                                $event->rrule(RRule::frequency(RecurrenceFrequency::$repeat_value())->interval($one_event->task_repeat_value));
                            };

                            array_push($array_events, $event);
                        }
                        $calendar = Calendar::create('Notificate.me ' . __('layout.menu_calendar'))
                            ->event($array_events)
                            ->withoutAutoTimezoneComponents()
                            ->refreshInterval(15);

                        return response($calendar->get(), 200, [
                            'Content-Type' => 'text/calendar; charset=utf-8',
                            'Content-Disposition' => 'attachment; filename="NotificateMeCalendar.ics"',
                        ]);
                    }
                }
            }
        }
    }
}
