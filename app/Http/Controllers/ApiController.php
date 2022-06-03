<?php

namespace App\Http\Controllers;

use App\Mail\NotificationMail;
use BulkGate\Message\Connection;
use BulkGate\Sms\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use BulkGate\Sms\Sender;
use BulkGate\Sms\SenderSettings;
use Ramsey\Uuid\Type\Integer;
use BulkGate\Sms\Country;

class ApiController extends Controller
{

    /**
     * Checks all tasks and their completion dates. If the due date is less than the current one, it will send an e-mail.
     */
    protected function AgentCheckDates()
    {

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
                    if ($new_date <= Carbon::now()->format('Y-m-d')) {
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
        };

    }

    /**
     * Skontroluje všetky úlohy. Následne pozrie či je aktívny sms modul a skontroluje či je zadané tel.č.
     * Následne pozrie úlohy ktorým za jeden den konči platnosť a rozopošle im sms
     * @return void
     */
    protected function AgentCheckDatesSMS()
    {

        $notification_array = array();

        $data = DB::table('tasks')
            ->join('users', 'tasks.user_id', '=', 'users.id')
            ->join('users_settings', 'tasks.user_id', '=', 'users_settings.user_id')
            ->join('modules', 'tasks.user_id', '=', 'modules.user_id')
            ->select('tasks.id', 'tasks.task_name', 'tasks.task_next_date', 'tasks.task_notification_type', 'users.name', 'users_settings.mobile_number', 'users_settings.notification_time', 'modules.module_sms')
            ->where([
                'tasks.task_enabled' => true,
                'tasks.sms_sent' => false,
                'modules.module_sms' => true
            ])
            ->get();

        if (!empty($data)) {
            foreach ($data as $item) {

                //Checks if the user wants to send notifications
                if ($item->module_sms == 0 || empty($item->mobile_number)) {
                    continue;
                };

                $checked_date = Carbon::createFromFormat('Y-m-d', $item->task_next_date);
                $new_date = $checked_date->subDay();
                $new_date = $new_date->format('Y-m-d');

                //check whether the current time is the same as set by the user
                $now = Carbon::now()->format('H');
                $date2 = Carbon::createFromFormat('H:i:s', $item->notification_time)->format('H');

                if ($now == $date2) {
                    if ($new_date == Carbon::now()->format('Y-m-d')) {
                        array_push($notification_array, [
                            'task_id' => $item->id,
                            'task_name' => $item->task_name,
                            'task_next_date' => $item->task_next_date,
                            'user_mobile_number' => $item->mobile_number,
                            'user_name' => $item->name
                        ]);
                    }
                }
            }
        }

        if (!empty($notification_array)) {
            foreach ($notification_array as $one_task) {
                return $this->sendSMS($one_task['task_id'], $one_task['task_name'], $one_task['user_name'], $one_task['task_next_date'], $one_task['user_mobile_number']);
            }
        }
    }

    /**
     * Odosielanie sms na uvedené čísla
     * @param int $task_id
     * @param string $task_name
     * @param string $name
     * @param string $checked_date
     * @param string $mobile_number
     * @return void
     * @throws SenderSettings\InvalidSenderException
     */
    public function sendSMS(int $task_id, string $task_name, string $name, string $checked_date, string $mobile_number)
    {

        if (str_contains($mobile_number, '421')) {
            $checked_date = Carbon::createFromFormat('Y-m-d', $checked_date)->format('d.m.Y');

            $connection = new Connection(env('BULKGATE_APP_ID'), env('BULKGATE_AUTH_TOKEN'));
            $sender = new Sender($connection);

            $type = SenderSettings\Gate::GATE_TEXT_SENDER;
            $value = 'Notificate';

            $settings = new SenderSettings\StaticSenderSettings($type, $value);
            $sender->setSenderSettings($settings);
            $sender->unicode(true);
            $sender->setDefaultCountry(Country::SLOVAKIA);

            $message_text = "Úloha: " . $task_name . ". Dátum splnenia: " . $checked_date;

            $message = new Message($mobile_number, $message_text);

            $sender->send($message);

            //Uprav úlohu aby sa iž nikdy znova neposlala sms
            DB::table('tasks')
                ->where('id', $task_id)
                ->update([
                    'sms_sent' => true
                ]);
        }


    }
}
