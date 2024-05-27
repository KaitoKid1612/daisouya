<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DriverTask;

/**
 * ドライバーレビューシーダー
 */
class DriverTaskReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result = '';

        if (config("app.env") === "local") {
            $data = [
                [
                    'id' => 1,
                    'driver_task_id' => 3,
                    'score' => 4,
                    'title' => 'タイトル',
                    'text' => 'helloハローレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキスト',
                    'driver_id' => 1,
                    'delivery_office_id' => 1,
                    'driver_task_review_public_status_id' => 1,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 2,
                    'driver_task_id' => 2,
                    'score' => 3,
                    'title' => 'タイトル',
                    'text' => 'レビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキスト',
                    'driver_id' => 1,
                    'delivery_office_id' => 1,
                    'driver_task_review_public_status_id' => 1,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];

            $result = DB::table('driver_task_reviews')->insertOrIgnore($data);

            /**
             * 稼働依頼からレビュー生成
             */
            $driver_task_list = DriverTask::select()->whereIn('driver_task_status_id', [4, 8])->get();

            foreach ($driver_task_list as $key => $item) {
                $rand = mt_rand(1, 2);
                if ($rand == 2) {
                    continue;
                }
                $driver_task_id = $item->id;
                $driver_id = $item->driver_id;
                $delivery_office_id = $item->delivery_office_id;

                if ($driver_task_id < 10) {
                    continue;
                }

                $data2 = [
                    'driver_task_id' => $driver_task_id,
                    'score' => mt_rand(1, 5),
                    'title' => 'タイトル',
                    'text' => 'レビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキストレビューテキスト',
                    'driver_id' => $driver_id,
                    'delivery_office_id' => $delivery_office_id,
                    'driver_task_review_public_status_id' => 1,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ];

                $result = DB::table('driver_task_reviews')->insertOrIgnore($data2);
            }
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
