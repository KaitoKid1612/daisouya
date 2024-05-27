<?php

namespace Tests\Unit\Api\Normal\Driver;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestDoc;
use App\Libs\Test\TestConfig;

/**
 * ドライバーユーザー ユニットテスト
 */
class DriverTest extends TestCase
{
    private $section = "ドライバーユーザー";

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

        $query_param = [];
        $query_text = "?" . http_build_query($query_param);

        $url = "/api/driver/user/show/1" . $query_text;
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
     * 更新
     * 正常値テスト
     */
    public function testUpdate()
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
            "type" => "password",
            "current_password" => "test1234",
            "password" => "test1234",
            "password_confirmation" => "test1234"
        ];

        $url = "/api/driver/user/update" . $query_text;
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
