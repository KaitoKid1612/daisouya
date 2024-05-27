<?php

namespace Tests\Unit\Api\Normal\DeliveryOffice;

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
        $token_path = $testConfig->getDeliveryOfficeApiTokenPath();
        $api_token = Storage::disk("private")->get($token_path);
        $headers = $testConfig->getRequestHeaders([
            "Authorization" => $api_token
        ]);

        $query_param = [
            'driver_id' => 2,
            'available_date_from' => "2020-04-26",
            'available_date_to' => "3000-12-31"
        ];
        $query_text = "?" . http_build_query($query_param);

        $url = "/api/delivery-office/driver/driver-schedule" . $query_text;
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
