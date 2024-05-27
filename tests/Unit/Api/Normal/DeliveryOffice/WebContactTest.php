<?php

namespace Tests\Unit\Api\Normal\DeliveryOffice;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestDoc;
use App\Libs\Test\TestConfig;

/**
 * お問い合わせ
 */
class WebContactTest extends TestCase
{
    private $section = "お問い合わせ";

    /**
     * 作成
     * 正常値テスト
     */
    public function testStore()
    {
        echo "\n" . __METHOD__ . "\n\n";

        $testConfig = new TestConfig();
        $token_path = $testConfig->getDeliveryOfficeApiTokenPath();
        $api_token = Storage::disk("private")->get($token_path);
        $headers = $testConfig->getRequestHeaders([
            "Authorization" => $api_token
        ]);

        $query_param = [];
        $query_text = "?" . http_build_query($query_param);

        $request_body = [
            "user_type_id" => 2,
            "name_sei" => "山田",
            "name_mei" => "太郎",
            "name_sei_kana" => "ヤマダ",
            "name_mei_kana" => "タロウ",
            "email" => "t@x.com",
            "tel" => "1234123412",
            "web_contact_type_id" => 1,
            "title" => "APIテスト",
            "text" => "APIテストHello"
        ];

        $url = "/api/guest/web-contact/store" . $query_text;
        $response = $this->postJson($url, $request_body, $headers);

        $test_result = '';
        if ($response->getStatusCode() === 200) {
            $test_result = true;
        } else {
            $test_result = false;
        }
        $response->assertStatus(200);

        $request = $this->app['request'];

        $testDoc = new TestDoc();
        $testDoc->createCSVDeliveryOfficeAPI(
            $this->section,
            __CLASS__,
            __FUNCTION__,
            $request,
            $response,
            $test_result
        );
    }
}
