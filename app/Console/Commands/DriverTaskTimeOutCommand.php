<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Models\Driver;
use App\Models\DriverTask;
use App\Models\DriverTaskStatus;
use App\Models\DeliveryOffice;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;
use App\Models\WebNoticeLog;

use Illuminate\Mail\Mailable;
use App\Mail\DriverTaskUpdateSendDeliveryOfficeMail;
use App\Mail\DriverTaskUpdateSendAdminMail;

use Illuminate\Support\Facades\Mail;

use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;

/**
 * 受諾期限が切れた稼働のステータスを時間切れにする
 */
class DriverTaskTimeOutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'driver_task:time_out';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '受諾期限が切れた稼働のステータスを時間切れにする';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("start DriverTaskTimeOutCommand");

        $login_id = '';

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        $config_base = WebConfigBase::where('id', 1)->first();
        $config_system = WebConfigSystem::where('id', 1)->first();
        $task_time_out_later = $config_system->task_time_out_later; // 現在から何日後の稼働日を時間切れにするか

        $datetime = new \Datetime();
        $today_dt = new \DateTime($datetime->format('Y-m-d')); // 今日
        $today_dt->modify("+{$task_time_out_later} days"); // 今日 ＋ 設定日
        $task_date_time_out = $today_dt->format("Y-m-d"); // 時間切れにするtask_dateの日付


        // 稼働を取得
        $driver_task = DriverTask::select()
            ->where('task_date', '<=', $task_date_time_out) // 稼働日が(今日+設定日)以下
            ->where(function ($query) {
                $query->where('driver_task_status_id', 1) // 新規
                ->orWhere('driver_task_status_id', 2) // 新規(指名)
                ->orWhere('driver_task_status_id', 10); // 決済未設定
            })
            ->where('is_template', '!=', 1)
            ->get();
//         logger($driver_task->toArray());

        // メールで利用するデータ
        $data_mail = [
            "config_base" => $config_base,
            "config_system" => $config_system,
        ];

        foreach ($driver_task as $task) {
            $msg = '';

            $task->driver_task_status_id = 6; // 時間切れ
            $task->save();

            $office = DeliveryOffice::where('id', $task->delivery_office_id)->first();
            $driver = Driver::where('id', $task->driver_id)->first();

            $data_mail['office'] = $office;
            $data_mail['driver'] = $driver;
            $data_mail['task'] = $task;

            /* 依頼者へのメール */

            if ($office) {
                // 送り先
                $to_office = [
                    [
                        'email' => $office->email ?? '',
                        'name' => $office->name ?? '',
                    ],
                ];

                try {
                    Mail::to($to_office)->send(new DriverTaskUpdateSendDeliveryOfficeMail($data_mail)); // 送信
                    $msg_mail = 'メールを送信しました。';
                    $log_level = 7;
                    $notice_type = 1;
                } catch (\Throwable $e) {
                    $msg_mail = 'メール送信エラー(コマンド)';
                    $msg .= $msg_mail;
                    $log_level = 4;
                    $notice_type = 1;

                    $log_format = LogFormat::error(
                        $msg,
                        '',
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
                    // 通知ログ
                    WebNoticeLog::create([
                        'web_log_level_id' => $log_level,
                        'web_notice_type_id' => $notice_type,
                        'task_id' => $task->id,
                        'to_user_id' => $office->id,
                        'to_user_type_id' => $office->user_type_id,
                        'to_user_info' => ($office->joinUserType->name ?? '') . " / email:" . ($office->email ?? ''),
                        'user_id' => null,
                        'user_type_id' => null,
                        'user_info' => "cron",
                        'text' => "稼働依頼{$task->joinTaskStatus->name}",
                        'remote_addr' => $remote_addr,
                        'http_user_agent' => $http_user_agent,
                        'url' => $url,
                    ]);
                }
            }


            /* 管理者へのメール */
            $to_admin = [
                [
                    'email' => $config_system->email_notice,
                    'name' => "{$config_base->site_name}",
                ],
            ];
            try {
                Mail::to($to_admin)->send(new DriverTaskUpdateSendAdminMail($data_mail)); // 送信
                $msg_mail = 'メールを送信しました。';
                $log_level = 7;
                $notice_type = 1;
            } catch (\Throwable $e) {
                $msg_mail = 'メール送信エラー(コマンド)';
                $msg .= $msg_mail;
                $log_level = 4;
                $notice_type = 1;

                $log_format = LogFormat::error(
                    $msg,
                    '',
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
                    'web_notice_type_id' => 1,
                    'task_id' => $task->id,
                    'to_user_id' => null,
                    'to_user_type_id' => null,
                    'to_user_info' => "管理者 / email:{$config_system->email_notice}",
                    'user_id' => null,
                    'user_type_id' => null,
                    'user_info' => "cron",
                    'text' => "稼働依頼{$task->joinTaskStatus->name}",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $url,
                ]);
            }
        }
        Log::info("end DriverTaskTimeOutCommand");
        return 0;
    }
}
