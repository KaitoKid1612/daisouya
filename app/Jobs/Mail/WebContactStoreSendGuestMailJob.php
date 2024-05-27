<?php

namespace App\Jobs\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Models\WebNoticeLog;

use Illuminate\Support\Facades\Mail;
use App\Mail\WebContactStoreSendGuestMail;

/**
 * お問合せメールのジョブ to ゲスト
 */
class WebContactStoreSendGuestMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // 試行回数
    public $backoff = 3; // 待機時間

    private $to_user; // 送り先
    private $data_mail; // メールで利用するデータ
    private $login_user; // ログインユーザー
    private $login_id; // ログインID
    private $info_list; // IPアドレス

    /**
     * Create a new job instance.
     *
     * @param array $to_user 送り先
     * @param array $data_mail メールで利用するデータ
     * @param object $login_user ログインユーザーの情報
     * @param array $info_list さまざま情報
     * @return void
     */
    public function __construct(array $to_user, array $data_mail, object $login_user, array $info_list)
    {
        $this->to_user = $to_user ?? '';
        $this->data_mail = $data_mail ?? '';
        $this->login_user = $login_user ?? '';
        $this->login_id = $login_user->id ?? '';
        $this->info_list = $info_list ?? '';


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $to_guest = $this->to_user ?? '';
        $data_mail = $this->data_mail ?? '';
        $login_user = $this->login_user ?? '';
        $login_id = $this->login_user->id ?? '';
        $info_list = $this->info_list ?? '';

        $msg = "";
        $log_level = 7;
        $notice_type = 1;

        $remote_addr = $info_list['remote_addr'] ?? ''; // IPアドレス
        $http_user_agent = $info_list['http_user_agent'] ?? ''; // OSブラウザ
        $url = $info_list['url']; // URL ドメイン以降
        $file_path = $info_list['file_path'] ?? ''; // ファイルパス

        $config_base = $data_mail['config_base'];
        $config_system = $data_mail['config_system'];
        $contact = $data_mail['contact'];

        // メールで利用するデータ
        $data_mail = [
            "config_base" => $config_base,
            "config_system" => $config_system,
            'contact' => $contact,
        ];

        try {
            Mail::to($to_guest)->send(new WebContactStoreSendGuestMail($data_mail)); // 送信
            $msg = 'メールを送信しました。';
            $log_level = 7;
            $notice_type = 1;
        } catch (\Throwable $e) {
            $msg = 'メール送信エラー';
            $log_level = 4;
            $notice_type = 1;
            $log_format = LogFormat::error(
                $msg,
                $this->login_user->joinUserType->name ?? '',
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

            throw $e; // エラーをジョブに知らせる
        } finally {
            WebNoticeLog::create([
                'web_log_level_id' => $log_level,
                'web_notice_type_id' => $notice_type,
                'task_id' => null,
                'to_user_id' => $login_id ?? null,
                'to_user_info' => $login_user->get_user_type->name ?? "ゲスト" . "/ email:{$login_user->email}",
                'user_id' => $login_id,
                'user_info' => $login_user->get_user_type->name ?? "ゲスト" . "/ email:{$contact->email}",
                'text' => "お問い合わせ",
                'remote_addr' => $remote_addr,
                'http_user_agent' => $http_user_agent,
                'url' => $url,
            ]);
        }
    }

    public function failed(\Throwable $e)
    {
        Log::error(mb_substr($e->__toString(), 0, 1200));
    }
}
