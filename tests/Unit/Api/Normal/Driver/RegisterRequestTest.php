<?php

namespace Tests\Unit\Api\Normal\Driver;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestDoc;
use App\Libs\Test\TestConfig;

/**
 * 登録申請 ユニットテスト
 */
class RegisterRequestTest extends TestCase
{
    private $section = "登録申請";

    /**
     * 作成
     */
    public function testStore()
    {
        echo "\n" . __METHOD__ . "\n\n";

        $testConfig = new TestConfig();
        $token_path = $testConfig->getDriverApiTokenPath();
        // $api_token = Storage::disk("private")->get($token_path);
        $headers = $testConfig->getRequestHeaders([
            // "Authorization" => $api_token
        ]);

        $query_param = [];
        $query_text = "?" . http_build_query($query_param);

        $request_body = [
            "name_sei" => "API性",
            "name_mei" => "API名",
            "name_sei_kana" => "セイ",
            "name_mei_kana" => "メイ",
            "email" => "api@amazon.com",
            "gender_id" => 2,
            "birthday" => "2000-01-01",
            "post_code1" => "123",
            "post_code2" => "1234",
            "addr1_id" => 12,
            "addr2" => "api addr2",
            "addr3" => "api addr3",
            "addr4" => "api addr4",
            "tel" => "09012341234",
            "career" => "apiキャリアapiキャリアapiキャリアapiキャリアapiキャリア",
            "introduction" => "apiintrointro",
            "terms_service" => true
        ];

        $url = "/api/driver/register-request/store" . $query_text;
        $response = $this->postJson($url, $request_body, $headers);

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
