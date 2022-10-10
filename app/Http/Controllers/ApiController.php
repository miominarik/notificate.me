<?php

namespace App\Http\Controllers;

use App\Mail\NotificationMail;
use BulkGate\Message\Connection;
use BulkGate\Sms\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use BulkGate\Sms\Sender;
use BulkGate\Sms\SenderSettings;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Ramsey\Uuid\Type\Integer;
use BulkGate\Sms\Country;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->factory = (new Factory)->withServiceAccount(storage_path('app/private/notificateme-firebase-adminsdk-a0ed2-007ce012a5.json'));
        $this->messaging = $this->factory->createMessaging();
    }


    /**
     * Checks all tasks and their completion dates. If the due date is less than the current one, it will send an
     * e-mail.
     */
    protected function AgentCheckDates()
    {

        $notification_array = array();

        $data = DB::table('tasks')
            ->join('users', 'tasks.user_id', '=', 'users.id')
            ->join('users_settings', 'tasks.user_id', '=', 'users_settings.user_id')
            ->select('tasks.id', 'tasks.task_name', 'tasks.task_next_date', 'tasks.task_notification_value', 'tasks.task_notification_type', 'users.email', 'users_settings.enable_email_notification', 'users_settings.notification_time', 'users_settings.language')
            ->where([
                'task_enabled' => TRUE
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
                            'task_id' => $item->id,
                            'task_name' => $item->task_name,
                            'task_next_date' => $item->task_next_date,
                            'user_email' => $item->email,
                            'language' => $item->language,
                        ]);
                    }
                }
            }
        }

        if (!empty($notification_array)) {
            foreach ($notification_array as $one_task) {
                Mail::to($one_task['user_email'], $one_task['user_email'])->send(new NotificationMail($one_task['task_name'], $one_task['task_next_date'], $one_task['language'], $one_task['task_id'], $one_task['user_email']));
            }
        };

    }

    /**
     * Skontroluje všetky úlohy. Následne pozrie či je aktívny sms modul a skontroluje či je zadané tel.č.
     * Následne pozrie úlohy ktorým za jeden den konči platnosť a rozopošle im sms
     *
     * @return void
     */
    protected function AgentCheckDatesSMS()
    {

        $notification_array = array();

        $data = DB::table('tasks')
            ->join('users_settings', 'tasks.user_id', '=', 'users_settings.user_id')
            ->join('modules', 'tasks.user_id', '=', 'modules.user_id')
            ->select('tasks.id', 'tasks.task_name', 'tasks.user_id', 'tasks.task_next_date', 'tasks.task_notification_type', 'users_settings.mobile_number', 'users_settings.notification_time', 'modules.module_sms', 'tasks.task_notification_value')
            ->where([
                'tasks.task_enabled' => TRUE,
                'tasks.sms_sent' => FALSE,
                'modules.module_sms' => TRUE
            ])
            ->get();

        if (!empty($data)) {
            foreach ($data as $item) {

                //Checks if the user wants to send notifications
                if ($item->module_sms == 0 || empty($item->mobile_number)) {
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
                            'task_id' => $item->id,
                            'task_name' => $item->task_name,
                            'task_next_date' => $item->task_next_date,
                            'user_mobile_number' => $item->mobile_number,
                            'user_id' => $item->user_id,
                        ]);
                    }
                }
            }
        }

        if (!empty($notification_array)) {

            $connection = new Connection(env('BULKGATE_APP_ID'), env('BULKGATE_AUTH_TOKEN'));
            $sender = new Sender($connection);
            $type = SenderSettings\Gate::GATE_TEXT_SENDER;
            $value = 'Notificate';
            $settings = new SenderSettings\StaticSenderSettings($type, $value);
            $sender->setSenderSettings($settings);
            $sender->unicode(TRUE);
            $sender->setDefaultCountry(Country::SLOVAKIA);

            foreach ($notification_array as $one_task) {
                if (str_contains($one_task['user_mobile_number'], '421')) {
                    $checked_date = Carbon::createFromFormat('Y-m-d', $one_task['task_next_date'])->format('d.m.Y');
                    $message_text = "Úloha: " . $one_task['task_name'] . ". Dátum splnenia: " . $checked_date;
                    $message = new Message($one_task['user_mobile_number'], $message_text);
                    $sender->send($message);

                    //Uprav úlohu aby sa iž nikdy znova neposlala sms
                    DB::table('tasks')
                        ->where('id', $one_task['task_id'])
                        ->update([
                            'sms_sent' => TRUE
                        ]);

                    //Pridal riadok do logov
                    $this->save_sms_log($one_task['task_id'], $one_task['user_id'], $one_task['user_mobile_number']);

                    unset($checked_date, $message_text, $message);
                }
            }
        }
    }

    /**
     * Ulož nový log o odoslaní sms
     *
     * @param $task_id
     * @param $user_id
     * @param $mobile_number
     *
     * @return void
     */
    protected function save_sms_log($task_id, $user_id, $mobile_number)
    {
        if (isset($task_id) && isset($user_id) && isset($mobile_number) && !empty($task_id) && !empty($user_id) && !empty($mobile_number)) {
            DB::table('sms_history')
                ->insert([
                    'task_id' => $task_id,
                    'user_id' => $user_id,
                    'mobile_number' => $mobile_number,
                    'created_at' => Carbon::now()
                ]);
        }
    }

    protected function FCM_Token(Request $request)
    {
        if ($request->input('token') && $request->input('auth_token')) {

            $auth_token = Crypt::decryptString($request->input('auth_token'));
            if (str_contains($auth_token, '|')) {
                $auth_token = explode('|', $auth_token)[1];
            }

            $get_user_id = DB::table('sessions')
                ->select('user_id')
                ->where('token', '=', $auth_token)
                ->get();

            if (isset($get_user_id[0]->user_id)) {
                DB::table('fcm_tokens')
                    ->updateOrInsert(
                        [
                            'user_id' => $get_user_id[0]->user_id,
                            'android_id' => $request->input('android_id')
                        ],
                        [
                            'fcm_token' => $request->input('token'),
                            'device_name' => $request->input('device'),
                            'device_model' => $request->input('model'),
                            'android_id' => $request->input('android_id'),
                            'user_id' => $get_user_id[0]->user_id,
                            'updated_at' => Carbon::now()
                        ]
                    );
            };
        };
    }


    /**
     * Checks all tasks and their completion dates. If the due date is less than the current one, it will send an
     * e-mail.
     */
    protected function AgentCheckFCM()
    {

        $notification_array = array();

        $data = DB::table('tasks')
            ->join('users_settings', 'tasks.user_id', '=', 'users_settings.user_id')
            ->select('tasks.id', 'tasks.task_name', 'tasks.task_next_date', 'tasks.task_notification_value', 'tasks.task_notification_type', 'users_settings.notification_time', 'tasks.user_id')
            ->where([
                'task_enabled' => TRUE
            ])
            ->get();
        if (!empty($data)) {
            foreach ($data as $item) {

                //Checks if the user wants to send notifications
                /*if ($item->enable_email_notification == 0) {
                    continue;
                };*/

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
                            'task_next_date' => Carbon::parse($item->task_next_date)->format('d.m.Y'),
                            'user_id' => $item->user_id,
                        ]);
                    }
                }
            }
        }
        if (!empty($notification_array)) {
            foreach ($notification_array as $one_task) {
                $body = "Blíži sa termín Vašej naplánovanej úlohy. ";
                $body .= "Názov úlohy: " . $one_task['task_name'] . " ";
                $body .= "Termín: " . $one_task['task_next_date'];
                return $this->sendNotification("Notificate.me", $body, $one_task['user_id']);
            }
        };

    }

    protected function sendNotification(string $title, string $body, int $user_id)
    {
        $all_fcm_tokens = DB::table('fcm_tokens')
            ->select('fcm_token')
            ->where('user_id', '=', $user_id)
            ->where('enabled', '=', 1)
            ->get();


        if (!empty($all_fcm_tokens)) {
            $all_tokens = array();
            foreach ($all_fcm_tokens as $one_fcm_token) {
                array_push($all_tokens, $one_fcm_token->fcm_token);
            }

            $result = $this->messaging->validateRegistrationTokens($all_tokens);

            $validated_tokens = array();

            foreach ($all_tokens as $one_token) {
                if (in_array($one_token, $result['valid'])) {
                    array_push($validated_tokens, $one_token);
                }
            }

            $config = AndroidConfig::fromArray([
                'ttl' => '10800s',
                'notification' => [
                    'icon' => 'ic_baseline_notifications_active_24',
                    'color' => '#1C3879',
                    'sound' => 'default',
                ],
            ]);

            $notification = Notification::fromArray([
                'title' => $title,
                'body' => $body
            ]);

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withHighestPossiblePriority()
                ->withAndroidConfig($config);

            if (is_array($validated_tokens) && !empty($validated_tokens)) {
                $send_status = $this->messaging->sendMulticast($message, $validated_tokens);

                if ($send_status->hasFailures()) {
                    foreach ($send_status->failures()->getItems() as $failure) {
                        echo $failure->error()->getMessage() . PHP_EOL;
                    }
                }
                unset($validated_tokens, $all_tokens, $all_fcm_tokens, $message, $notification, $config, $send_status);
            }

        };

    }
}
