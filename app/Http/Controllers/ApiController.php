<?php

namespace App\Http\Controllers;

use App\Mail\NotificationMail;
use BulkGate\Message\Connection;
use BulkGate\Sms\Message;
use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use BulkGate\Sms\Sender;
use BulkGate\Sms\SenderSettings;
use Ramsey\Uuid\Type\Integer;
use BulkGate\Sms\Country;
use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message as MessageFCM;
use sngrl\PhpFirebaseCloudMessaging\Notification;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->FCM_client = new Client();
        $this->FCM_client->setApiKey("AAAAgl2WWSw:APA91bGpVwK8VIUazGfRQG6WD8X9qdYXLzSylZgVXpmY4GSzTzC_1j3rsvz_Ifa90J6xi_VluD39sZKrK6UA4eMB-uYRA736uxMXPvPXqT5Hhpz0wP5Xz2DSPU89yfYolZMhnRMcwmG5");
        $this->FCM_client->injectGuzzleHttpClient(new \GuzzleHttp\Client());
    }


    /**
     * Checks all tasks and their completion dates. If the due date is less than the current one, it will send an e-mail.
     */
    protected function AgentCheckDates()
    {

        $notification_array = array();

        $data = DB::table('tasks')
            ->join('users', 'tasks.user_id', '=', 'users.id')
            ->join('users_settings', 'tasks.user_id', '=', 'users_settings.user_id')
            ->select('tasks.id', 'tasks.task_name', 'tasks.task_next_date', 'tasks.task_notification_value', 'tasks.task_notification_type', 'users.email', 'users_settings.enable_email_notification', 'users_settings.notification_time', 'users_settings.language')
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
            $sender->unicode(true);
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
                            'sms_sent' => true
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
     * @param $task_id
     * @param $user_id
     * @param $mobile_number
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

    protected function FCM_Token($token, $device_name, $device_model, $android_id)
    {
        if ($token && Auth::check()) {
            DB::table('fcm_tokens')
                ->updateOrInsert(
                    [
                        'user_id' => Auth::id(),
                        'android_id' => $android_id
                    ],
                    [
                        'fcm_token' => $token,
                        'device_name' => $device_name,
                        'device_model' => $device_model,
                        'android_id' => $android_id,
                        'user_id' => Auth::id(),
                        'updated_at' => Carbon::now()
                    ]
                );
        };

        return redirect(route('login'));
    }


    /**
     * Checks all tasks and their completion dates. If the due date is less than the current one, it will send an e-mail.
     */
    protected function AgentCheckFCM()
    {

        $notification_array = array();

        $data = DB::table('tasks')
            ->join('users_settings', 'tasks.user_id', '=', 'users_settings.user_id')
            ->select('tasks.id', 'tasks.task_name', 'tasks.task_next_date', 'tasks.task_notification_value', 'tasks.task_notification_type', 'users_settings.notification_time', 'tasks.user_id')
            ->where([
                'task_enabled' => true
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
                return $this->sendNotification($this->FCM_client, "Notificate.me", $body, $one_task['user_id']);
            }
        };

    }

    protected function sendNotification(Client $client, string $title, string $body, int $user_id)
    {
        $message = new MessageFCM();
        $message->setNotification(new Notification($title, $body));
        $message->setTimeToLive(10800);
        $message->setPriority("normal");

        //získanie všetkých FCM tokenov
        $all_fcm_tokens = DB::table('fcm_tokens')
            ->select('fcm_token')
            ->where('user_id', '=', $user_id)
            ->where('enabled', '=', 1)
            ->get();

        if (!empty($all_fcm_tokens)) {
            foreach ($all_fcm_tokens as $one_fcm_token) {
                $message->addRecipient(new Device($one_fcm_token->fcm_token));
            }
            $client->send($message);
        };


    }
}
