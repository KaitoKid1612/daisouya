<?php

namespace Tests\Unit\Api\Normal\DeliveryOffice;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestDoc;
use App\Libs\Test\TestConfig;

/**
 * ドライバー ユニットテスト
 */
class DriverTest extends TestCase
{
    private $section = "ドライバー";

    /**
     * 一覧 
     * 正常値テスト
     */
    public function testIndex()
    {
        echo "\n" . __METHOD__ . "\n\n";

        $testConfig = new TestConfig();
        $token_path = $testConfig->getDeliveryOfficeApiTokenPath();
        $api_token = Storage::disk("private")->get($token_path);
        $headers = $testConfig->getRequestHeaders([
            "Authorization" => $api_token
        ]);

        $query_param = [
            'page' => 2,
        ];
        $query_text = "?" . http_build_query($query_param);

        $url = "/api/delivery-office/driver" . $query_text;
        $response = $this->getJson($url, $headers);
        
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

    /**
     * 取得 
     * 正常値テスト
     */
    public function testShow()
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

        $url = "/api/delivery-office/driver/show/3" . $query_text;
        $response = $this->getJson($url, $headers);
        
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
