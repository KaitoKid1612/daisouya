<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use Illuminate\Support\Facades\Storage;

class TestPushCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test_push {registration_token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $path = "daisouya-firebase-adminsdk.json";
        // $path = "google-services.json";
        $is_path = Storage::disk('private')->exists($path);
        if ($is_path) {
            $path_firebase_json = Storage::disk('private')->path($path);
        }
        $registrationToken = $this->argument('registration_token');
        $factory = (new Factory)->withServiceAccount($path_firebase_json);
        $messaging = $factory->createMessaging();

        $notification = Notification::fromArray([
            'title' => 'Hello World Title! 代走屋APP',
            'body' => 'Hello World Body! 代走屋APP',
        ]);

        $message = CloudMessage::withTarget('token', $registrationToken)
            ->withNotification($notification);

        $messaging->send($message);
        // // ブラウザから取得した登録トークン
        // $registration_token = '登録トークン';


        // // 事前にダウンロードした認証情報のJSONを読み込む
        // $serviceAccount = ServiceAccount::fromJsonFile($path_firebase_json);

        // $firebase = (new Factory)
        //     ->withServiceAccount($serviceAccount)
        //     ->create();

        // $messaging = $firebase->getMessaging();

        // // FCMに送信するデータの作成
        // $notification = Notification::fromArray([
        //     'title' => 'TGIF!!!!!!!!!!',
        //     'body'  => 'Thank God It\'s Friday'
        // ]);
        // $config = WebPushConfig::fromArray([
        //     'fcm_options' => [
        //         'link' => 'https://firebase-php.readthedocs.io/en/latest/cloud-messaging.html#webpush',
        //     ],
        // ]);

        // $message = CloudMessage::withTarget('token', $registration_token)
        //     ->withNotification($notification)
        //     ->withWebPushConfig($config);

        // $messaging->send($message);

        return 0;
    }
}
