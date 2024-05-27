<?php

namespace Tests\Unit\Api\Normal\Other;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestDoc;
use App\Libs\Test\TestConfig;

/**
 * プライバシーポリシー ユニットテスト
 */
class WebPrivacyPolicyTest extends TestCase
{
    private $section = "プライバシーポリシー";

    /**
     * 一覧
     * 正常値テスト
     */
    public function testIndex()
    {
        echo "\n" . __METHOD__ . "\n\n";

        $testConfig = new TestConfig();
        $headers = $testConfig->getRequestHeaders();

        $query_param = [
            "type" => "driver",
            "is_html" => "0"
        ];
        $query_text = "?" . http_build_query($query_param);

        $url = "/api/guest/privacy-policy" . $query_text;
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
