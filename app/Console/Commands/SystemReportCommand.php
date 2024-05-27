<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use App\Models\WebConfigSystem;
use App\Models\WebConfigBase;
use App\Models\Admin;
use App\Mail\SystemReportSendAdmin;

use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use App\Models\WebNoticeLog;
use App\Models\FailedJob;
use App\Models\WebPaymentLog;

use Yasumi\Yasumi;

use App\Services\SlackNotificationService;

/**
 * システムレポート通知
 */
class SystemReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:system_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'システムの結果を通知する';

    protected $slack_notification_service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SlackNotificationService $slack_notification_service)
    {
        parent::__construct();
        $this->slack_notification_service =  $slack_notification_service;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $class = __CLASS__;
        Log::info("Start Cron {$class}");

        $result = ''; // 通知するテキスト

        /* グローバルIP */
        $result .= "-グローバルIP: ";
        try {
            $shell_command = 'sudo curl inet-ip.info';
            $text = shell_exec($shell_command);
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }
        $result .= "\n";

        $result .= "-.env: ";
        $result .= "\n";
        try {
            $list = ['app.name', 'app.env', 'app.debug', 'app.url'];

            foreach ($list as $val) {
                $result .= "{$val}=";
                $result .= config($val) ?? '';
                $result .= "\n";
            }
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }
        $result .= "\n";

        /**
         * エラーログ収集
         */
        try {
            $interval = new \DateInterval('P1D'); // 1日ごとに取得する
            $today = new \DateTime(); // 現在日時
            $one_week_ago = clone $today;
            $one_week_ago->modify('-6 days'); // 1週間前

            $week_today = $today->format('w'); // 今日の曜日(数値)

            $tomorrow = new \DateTime('tomorrow');
            $date_range = new \DatePeriod($one_week_ago, $interval, $tomorrow);

            $day_count = (int)iterator_count($date_range) - 1; // オブジェクトの要素数取得

            $result .= "--ログ--\n";
            $result .= "-システムエラー\n";
            foreach ($date_range as $key => $date) {
                $weekday = $date->format('D');
                $date_str = $date->format('Y-m-d');
                $path = "/error/error-{$date_str}.log";
                $match_count = 0; // 正規表現パターンにマッチした数

                if (Storage::disk('logs')->exists($path)) {
                    $log =  Storage::disk('logs')->get($path);

                    /* [2023-04-27 11:09:19] testing.ERROR: のようなパターン */
                    $pattern = '/\[(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})\]\s+(\w+)\.(\w+)\:/';

                    $match_count = preg_match_all($pattern, $log, $data);
                }
                $result .= "{$date_str}({$weekday}): {$match_count}";
                if ($key < $day_count) {
                    $result .= "\n";
                }
            }
        } catch (\Throwable $e) {
            log::error($e);
        }
        $result .= "\n\n";


        /* ジョブエラー */
        $result .= "-過去1週間のFailed Job Count: ";
        try {
            // 現在時刻
            $now = new \DateTime();
            $one_week_ago = clone $now;
            $one_week_ago->modify('-1 week');
            $count = FailedJob::where('failed_at', '>=', $one_week_ago->format('Y-m-d'))->count();
            $result .= $count;
        } catch (\Throwable $e) {
            $result .= "Error";
            Log::error($e);
        }
        $result .= "\n";


        /* 通知ログ */
        $result .= "-過去1週間の通知ログ: ";
        try {
            // 現在時刻
            $now = new \DateTime();
            $one_week_ago = clone $now;
            $one_week_ago->modify('-1 week');
            $count_all = WebNoticeLog::where([
                ['created_at', '>=', $one_week_ago->format('Y-m-d')],
            ])->count();
            $count_error = WebNoticeLog::where([
                ['created_at', '>=', $one_week_ago->format('Y-m-d')],
                ['web_log_level_id', 4],
            ])->count();
            $result .= "All:{$count_all} Error:{$count_error}";
        } catch (\Throwable $e) {
            $result .= "Error";
            Log::error($e);
        }
        $result .= "\n";

        /**
         * Push通知エラーログ収集
         */
        $result .= "-過去1週間のPush通知エラーログ: ";
        try {
            $interval = new \DateInterval('P1D'); // 1日ごとに取得する
            $today = new \DateTime(); // 現在日時
            $one_week_ago = clone $today;
            $one_week_ago->modify('-6 days'); // 1週間前

            $week_today = $today->format('w'); // 今日の曜日(数値)

            $date_range = new \DatePeriod($one_week_ago, $interval, $today->modify('+1 day'));

            $day_count = (int)iterator_count($date_range) - 1; // オブジェクトの要素数取得


            $count = 0;
            foreach ($date_range as $key => $date) {
                $weekday = $date->format('D');
                $date_str = $date->format('Y-m-d');
                $path = "/error/error-{$date_str}.log";
                $match_count = 0; // 正規表現パターンにマッチした数

                if (Storage::disk('logs')->exists($path)) {
                    $log =  Storage::disk('logs')->get($path);

                    /* [2023-04-27 11:09:19] testing.ERROR: Message Push通知エラー */
                    $pattern = '/\[(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})\]\s+(\w+)\.(\w+)\:.*Push/i';

                    $match_count = preg_match_all($pattern, $log, $data);
                    $count += $match_count;
                }
            }
            $result .= "{$count}";
        } catch (\Throwable $e) {
            log::error($e);
            $result .= "Error";
        }
        $result .= "\n";

        /* 通知ログ */
        $result .= "-過去1週間の決済ログ: ";
        try {
            // 現在時刻
            $now = new \DateTime();
            $one_week_ago = clone $now;
            $one_week_ago->modify('-1 week');
            $count_all = WebPaymentLog::where([
                ['created_at', '>=', $one_week_ago->format('Y-m-d')],
            ])->count();
            $count_error = WebPaymentLog::where([
                ['created_at', '>=', $one_week_ago->format('Y-m-d')],
                ['id', 2],
            ])->count();
            $result .= "All:{$count_all} Error:{$count_error}";
        } catch (\Throwable $e) {
            $result .= "Error";
            Log::error($e);
        }
        $result .= "\n";

        $result .= "\n--死活監視--\n";

        /*Apache起動確認 */
        $result .= "-Apache: ";
        try {
            $shell_command = 'sudo systemctl is-active httpd.service';
            $text = shell_exec($shell_command);
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }

        /* SQL */
        $result .= "-SQL起動確認: ";
        try {
            $shell_command = 'sudo systemctl is-active mysqld.service ';
            $text = shell_exec($shell_command);
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }

        $result .= "-SQL Connect: ";
        try {
            DB::connection()->getPdo();
            $text = "Success";
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }
        $result .= "\n";

        /* Redis */
        $result .= "-Redis起動確認: ";
        try {
            $shell_command = 'sudo systemctl is-active redis.service';
            $text = shell_exec($shell_command);
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }

        $result .= "-Redis Connect: ";
        try {
            $redis = Redis::ping();
            $text = "Success";
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }
        $result .= "\n";

        $result .= "-Redisキューに溜まっているジョブの数: ";
        try {
            $queues_name = 'queues:default';
            $job_count = Redis::command('LLEN', [$queues_name]);
            $result .= $job_count;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }
        $result .= "\n";


        /* Supervisord */
        $result .= "-Supervisord起動確認: ";
        try {
            $shell_command = 'sudo systemctl is-active supervisord.service';
            $text = shell_exec($shell_command);
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }

        $result .= "-Supervisord status:";
        try {
            $shell_command = 'sudo supervisorctl status';
            $text = shell_exec($shell_command);
            $result .= "\n{$text}";
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }
        $result .= "\n";

        /* Cron */
        $result .= "-Cron起動: ";
        try {
            $shell_command = 'sudo systemctl is-active crond.service';
            $text = shell_exec($shell_command);
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }

        $result .= "-Cron設定:\n";
        try {
            $shell_command = 'crontab -l';
            $text = shell_exec($shell_command) ?? 'No!!';
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }

        $result .= "-Laravel schedule:list:\n";
        try {
            $shell_command = 'sudo php artisan schedule:list';

            // コマンドを実行してプロセスの状態を取得する
            $text = shell_exec($shell_command);
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }
        $result .= "\n";

        $result .= "-常時起動Linuxサービス:\n";
        try {
            $shell_command = "sudo systemctl list-unit-files --type=service | egrep 'crond.service|mysqld.service|httpd.service|supervisord.service|redis.service'";

            $text = shell_exec($shell_command);
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }
        $result .= "\n";


        $result .= "--リソース状況--\n";
        $result .= "-CPU使用率:\n";
        try {
            $shell_command = "sudo top -bn1 | grep 'Cpu(s)'";

            $text = shell_exec($shell_command);
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }
        $result .= "\n";

        $result .= "-メモリ使用状況:\n";
        try {
            $shell_command = 'sudo free -h';

            $text = shell_exec($shell_command);
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }
        $result .= "\n";

        $result .= "-ストレージ使用状況:\n";
        try {
            $shell_command = 'sudo df -h';

            $text = shell_exec($shell_command);
            $result .= $text;
        } catch (\Throwable $e) {
            $result .= "Error\n";
            Log::error($e);
        }
        $result .= "\n";


        Log::info($result);
        // exit;

        /* 通知処理 */
        // 祝日を扱うYasumiオブジェクト
        $today = new \DateTime(); // 現在日時
        $week_today = $today->format('w'); // 今日の曜日(数値)
        $holidays = Yasumi::create('Japan', $today->format("Y"));
        $is_holiday = $holidays->isHoliday($today); // 祝日か判定

        // 平日なら通知
        if (in_array($week_today, [1, 2, 3, 4, 5]) && !$is_holiday) {
            $config_system = WebConfigSystem::where('id', 1)->first();
            $config_base = WebConfigBase::where('id', 1)->first();
            $admin =  Admin::where('admin_permission_group_id', 1)->first();

            $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
            $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
            $file_path = __FILE__; // ファイルパス

            /* Slackに通知 */
            $notice_type = 3;
            try {
                $slack_message = "システムレポート\n" . $result;
                $this->slack_notification_service->send($slack_message);
                // 送信
                $msg = 'Slack送信しました。';
                $log_level = 7;
                $notice_type = 3;
            } catch (\Throwable $e) {
                $msg = 'Slack送信エラー(コマンド)';
                $log_level = 4;

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
                    'web_notice_type_id' => $notice_type,
                    'task_id' => null,
                    'to_user_id' => $admin->id,
                    'to_user_type_id' => $admin->user_type_id,
                    'to_user_info' => "Slack",
                    'user_id' => null,
                    'user_type_id' => null,
                    'user_info' => "command",
                    'text' => "システムレポート",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $file_path,
                ]);
            }



            $to_admin = [
                [
                    'email' => $admin->email,
                    'name' => $admin->full_name,
                ],
            ];

            $data_mail = [
                'result' => $result,
                'config_system' => $config_system,
                'config_base' => $config_base,
            ];


            $msg = "";
            $log_level = 7;
            $notice_type = 1;
            try {
                Mail::to($to_admin)->send(new SystemReportSendAdmin($data_mail)); // 送信
                // 送信
                $msg = 'メールを送信しました。';
                $log_level = 7;
                $notice_type = 1;
            } catch (\Throwable $e) {
                $msg = 'メール送信エラー(コマンド)';
                $log_level = 4;

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
                    'web_notice_type_id' => $notice_type,
                    'task_id' => null,
                    'to_user_id' => $admin->id,
                    'to_user_type_id' => $admin->user_type_id,
                    'to_user_info' => "{$admin->joinAdminPermissionGroup->name} / email:{$admin->email}",
                    'user_id' => null,
                    'user_type_id' => null,
                    'user_info' => "command",
                    'text' => "システムレポート",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $file_path,
                ]);
            }
        } else {
            Log::info("休みなので通知しません。 {$class}");
        }


        Log::info("End Cron {$class}");
        return 0;
    }
}
