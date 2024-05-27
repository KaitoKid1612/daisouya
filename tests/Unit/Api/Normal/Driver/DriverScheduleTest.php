<?php

namespace Tests\Unit\Api\Normal\Driver;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestDoc;
use App\Libs\Test\TestConfig;

/**
 * ドライバースケジュール ユニットテスト
 */
class DriverScheduleTest extends TestCase
{
    private $section = "ドライバースケジュール";

    /**
     * 一覧 
     * 正常値テスト
     */
    public function testIndex()
    {
        echo "\n" . __METHOD__ . "\n\n";

        $testConfig = new TestConfig();
        $token_path = $testConfig->getDriverApiTokenPath();
        $api_token = Storage::disk("private")->get($token_path);
        $headers = $testConfig->getRequestHeaders([
            "Authorization" => $api_token
        ]);

        $query_param = [
            "available_date_from" => "2022-04-01",
            "available_date_to" => "2099-12-23"
        ];
        $query_text = "?" . http_build_query($query_param);

        $url = "/api/driver/driver-schedule" . $query_text;
        $response = $this->getJson($url, $headers);

        $test_result = '';
        if ($response->getStatusCode() === 200) {
            $test_result = true;
        } else {
            $test_result = false;
        }
        $response->assertStatus(200);

        $response_body = json_decode($response->getContent());
        $api_status = $response_body->status;
        $this->assertTrue($api_status, $message = "APIステータスがTrueではない!");

        $request = $this->app['request'];

        $testDoc = new TestDoc();
        $testDoc->createCSVDriverAPI(
            $this->section,
            __CLASS__,
            __FUNCTION__,
            $request,
            $response,
            $test_result
        );
    }

    /**
     * 作成
     * 正常値テスト
     */
    public function testStore()
    {
        echo "\n" . __METHOD__ . "\n\n";

        $testConfig = new TestConfig();
        $token_path = $testConfig->getDriverApiTokenPath();
        $api_token = Storage::disk("private")->get($token_path);
        $headers = $testConfig->getRequestHeaders([
            "Authorization" => $api_token
        ]);

        $query_param = [];
        $query_text = "?" . http_build_query($query_param);

        $request_body = [
            "available_date" => [
                "2023-04-12",
                "2023-04-08",
                "2023-05-01",
                "2023-06-11"
            ]
        ];

        $url = "/api/driver/driver-schedule/store" . $query_text;
        $response = $this->postJson($url, $request_body,  $headers);

        $test_result = '';
        if ($response->getStatusCode() === 200) {
            $test_result = true;
        } else {
            $test_result = false;
        }
        $response->assertStatus(200);

        $response_body = json_decode($response->getContent());
        $api_status = $response_body->status;
        $this->assertTrue($api_status, $message = "APIステータスがTrueではない!");

        $request = $this->app['request'];

        $testDoc = new TestDoc();
        $testDoc->createCSVDriverAPI(
            $this->section,
            __CLASS__,
            __FUNCTION__,
            $request,
            $response,
            $test_result
        );
    }

    /**
     * 削除
     * 正常値テスト
     */
    public function testDestroy()
    {
        echo "\n" . __METHOD__ . "\n\n";

        $testConfig = new TestConfig();
        $token_path = $testConfig->getDriverApiTokenPath();
        $api_token = Storage::disk("private")->get($token_path);
        $headers = $testConfig->getRequestHeaders([
            "Authorization" => $api_token
        ]);

        $query_param = [];
        $query_text = "?" . http_build_query($query_param);

        $request_body = [];

        $url = "/api/driver/driver-schedule/destroy/5" . $query_text;
        $response = $this->postJson($url, $request_body,  $headers);

        $test_result = '';
        if ($response->getStatusCode() === 200) {
            $test_result = true;
        } else {
            $test_result = false;
        }
        $response->assertStatus(200);

        $response_body = json_decode($response->getContent());
        $api_status = $response_body->status;
        $this->assertTrue($api_status, $message = "APIステータスがTrueではない!");

        $request = $this->app['request'];

        $testDoc = new TestDoc();
        $testDoc->createCSVDriverAPI(
            $this->section,
            __CLASS__,
            __FUNCTION__,
            $request,
            $response,
            $test_result
        );
    }
}
