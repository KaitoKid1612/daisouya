<?php

namespace Tests\Unit\Api\Normal\Guest;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestDoc;
use App\Libs\Test\TestConfig;


/**
 * 登録申請ステータス ユニットテスト
 */

class RegisterRequestStatusTest extends TestCase
{
    private $section = "登録申請ステータス";

    /**
     * 一覧
     * 正常値テスト
     */

    public function testIndex()
    {
        echo "\n" . __METHOD__ . "\n\n";

        $testConfig = new TestConfig();
        $headers = $testConfig->getRequestHeaders();

        $query_param = [];
        $query_text = "?" . http_build_query($query_param);

        $url = "/api/guest/register-request-status" . $query_text;
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
        $testDoc->createCSVOtherAPI(
            $this->section,
            __CLASS__,
            __FUNCTION__,
            $request,
            $response,
            $test_result
        );
    }

    /**
     * 取得
     * 正常値テスト
     */
    public function testShow()
    {
        echo "\n" . __METHOD__ . "\n\n";

        $testConfig = new TestConfig();
        $headers = $testConfig->getRequestHeaders();

        $query_param = [];
        $query_text = "?" . http_build_query($query_param);

        $url = "/api/guest/register-request-status/show/2" . $query_text;
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
        $testDoc->createCSVOtherAPI(
            $this->section,
            __CLASS__,
            __FUNCTION__,
            $request,
            $response,
            $test_result
        );
    }
}
