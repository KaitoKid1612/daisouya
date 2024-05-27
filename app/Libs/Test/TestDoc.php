<?php

namespace App\Libs\Test;

use Illuminate\Support\Facades\Storage;

/**
 * テスト結果の資料を扱う
 */
class TestDoc
{

    /**
     * 依頼者のAPIテスト結果の資料作成
     */
    public function createCSVDeliveryOfficeAPI(
        $section,
        $class,
        $function,
        $request,
        $response,
        $test_result
    ) {
        $csv_path = "unit_test/unit_test_evidence_delivery_office_api.csv";

        $this->createCSV(
            $csv_path,
            $section,
            $class,
            $function,
            $request,
            $response,
            $test_result
        );
    }

    /**
     * ドライバーのAPIテスト結果の資料作成
     */
    public function createCSVDriverAPI(
        $section,
        $class,
        $function,
        $request,
        $response,
        $test_result
    ) {
        $csv_path = "unit_test/unit_test_evidence_driver_api.csv";

        $this->createCSV(
            $csv_path,
            $section,
            $class,
            $function,
            $request,
            $response,
            $test_result
        );
    }


    /**
     * ゲストやその他のAPIテスト結果の資料作成
     */
    public function createCSVOtherAPI(
        $section,
        $class,
        $function,
        $request,
        $response,
        $test_result
    ) {
        $csv_path = "unit_test/unit_test_evidence_guest_api.csv";

        $this->createCSV(
            $csv_path,
            $section,
            $class,
            $function,
            $request,
            $response,
            $test_result
        );
    }


    /**
     * ユニットテストのCSVを作成
     * 
     * @param string $csv_path 出力するCSVのパス
     * @param string $section テスト区分
     * @param string $class テスト実行クラス名
     * @param string $function テスト実行関数名
     * @param object $request リクエストデータ
     * @param object $response レスポンスデータ
     * @param object $test_result テスト結果
     */
    protected function createCSV(
        $csv_path,
        $section,
        $class,
        $function,
        $request,
        $response,
        $test_result
    ) {

        $request_url = $request->fullUrl(); // リクエストパス
        $request_http_method = $request->method();
        $request_headers = $request->headers->all(); // リクエストヘッダー
        $request_body = $request->getContent(); // リクエストボディ

        $response_body = json_decode($response->getContent());
        $api_status = $response_body->status ?? ''; // APIのステータス

        // ディレクトリが存在しない場合、ディレクトリを作成
        $directory = "unit_test";
        if (!Storage::disk('private')->exists($directory)) {
            Storage::disk('private')->makeDirectory($directory);
        }

        // csvファイルが存在しない場合、csvを作成
        if (!Storage::disk('private')->exists($csv_path)) {
            $create_file = Storage::disk('private')->path($csv_path);
            touch($create_file);

            $csv_storage_path = Storage::disk('private')->path($csv_path);
            $csv = fopen($csv_storage_path, 'a');

            $csv_headdr = [
                'テスト結果',
                '区分',
                'APIステータス',
                'ステータス',
                'クラス',
                'メソッド名',
                '実行URL',
                'HTTPメソッド',
                'リクエストヘッダー',
                'リクエストボディ',
                'レスポンスヘッダー',
                'レスポンスボディ',
            ];
            fputcsv($csv, $csv_headdr); // 新しいデータをCSVファイルに追加

            fclose($csv);
        }

        if (Storage::disk('private')->exists($csv_path)) {
            $csv_storage_path = Storage::disk('private')->path($csv_path);
            $csv = fopen($csv_storage_path, 'a');

            // 新しいレコードを追加する
            $new_data = [
                $test_result ? '成功' : '失敗',
                $section,
                $api_status ? '成功' : '失敗',
                $response->getStatusCode(),
                $class,
                $function,
                $request_url,
                $request_http_method,
                json_encode($request_headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                json_encode(json_decode($request_body), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                json_encode($response->headers->all(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                json_encode(json_decode($response->getContent()), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ];

            fputcsv($csv, $new_data); // レコードをCSVファイルに追加

            fclose($csv);
        }
        return True;
    }
}
