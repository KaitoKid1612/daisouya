<?php

namespace App\Jobs\Push;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Libs\Log\LogFormat;
use Illuminate\Support\Facades\Log;
use App\Models\WebNoticeLog;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Storage;

/**
 * Push通知
 */
class PushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $to_user; // 送り先
    private $data_push; // Push通知で送るデータ
    private $login_user; // ログインユーザー
    private $login_id; // ログインID
    private $info_list; // IPアドレス
    private $data_other; // その他のデータ

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        object $to_user,
        array $data_push,
        object $login_user,
        array $info_list,
        array $data_other
    ) {
        $this->to_user = $to_user ?? '';
        $this->data_push = $data_push;
        $this->login_user = $login_user ?? '';
        $this->login_id = $login_user->id ?? '';
        $this->info_list = $info_list ?? '';
        $this->data_other = $data_other;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fcm_token = $this->data_push['fcm_token'] ?? '';
        $title = $this->data_push['title'] ?? '';
        $body = $this->data_push['body'] ?? '';
        $custom_data = $this->data_push['custom_data'] ?? [];

        $to_user = $this->to_user ?? '';
        $login_user = $this->login_user ?? '';
        $login_id = $this->login_user->id ?? '';
        $info_list = $this->info_list ?? '';

        $msg = "";
        $log_level = 7;
        $notice_type = 4;

        $remote_addr = $info_list['remote_addr'] ?? ''; // IPアドレス
        $http_user_agent = $info_list['http_user_agent'] ?? ''; // OSブラウザ
        $url = $info_list['url']; // URL ドメイン以降
        $file_path = $info_list['file_path'] ?? ''; // ファイルパス

        $task = $this->data_other['task'] ?? '';

        try {
            $log_level = 7;

            $path = "daisouya-firebase-adminsdk.json";
            $is_path = Storage::disk('private')->exists($path);
            if ($is_path) {
                $path_firebase_json = Storage::disk('private')->path($path);
            }

            $factory = (new Factory)->withServiceAccount($path_firebase_json);
            $messaging = $factory->createMessaging();

            // @todo デバッグするため
            $custom_data = [
                'to_user_type' => $to_user->joinUserType->name ?? '',
                'custom_key' => 'custom_value',
            ];

            $notification = Notification::fromArray([
                'title' => $title,
                'body' => $body,
            ]);

            $message = CloudMessage::withTarget('token', $fcm_token)
                ->withNotification($notification)->withData($custom_data);;

            $messaging->send($message);
        } catch (\Throwable $e) {
            $msg .= 'Push通知エラー';
            $log_level = 4;
            $notice_type = 4;

            $log_format = LogFormat::error(
                $msg,
                $login_user->joinUserType->name ?? '',
                $login_id ?? '',
                $remote_addr ?? '',
                $http_user_agent ?? '',
                $url ?? '',
                $file_path ?? '',
                $e->getCode(),
                $e->getFile(),
                $e->getLine(),
                mb_substr($e->__toString(), 0, 1200) . "......\nLog End",
            );
            Log::error($log_format);
        } finally {
            WebNoticeLog::create([
                'web_log_level_id' => $log_level,
                'web_notice_type_id' => $notice_type,
                'task_id' => $task->id ?? null,
                'to_user_id' => $to_user->id ?? null,
                'to_user_type_id' => $to_user->user_type_id ?? 4,
                'to_user_info' => ($to_user->joinUserType->name ?? '') . " / fcm_token:" . ($fcm_token ?? ''),
                'user_id' => $login_id ?? null,
                'user_type_id' => $login_user->user_type_id ?? 4,
                'user_info' => $login_user->joinUserType->name ?? '',
                'text' => "稼働依頼{$task->joinTaskStatus->name}",
                'remote_addr' => $remote_addr,
                'http_user_agent' => $http_user_agent,
                'url' => $url,
            ]);
        }
    }
}
