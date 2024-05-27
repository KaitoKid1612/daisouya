<?php

namespace Tests\Unit\Api\Normal\DeliveryOffice;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestDoc;
use App\Libs\Test\TestConfig;

/**
 * 稼働依頼プランとドライバープランの対応関係
 */
class DriverTaskPlanAllowDriverTest extends TestCase
{
    private $section = "稼働依頼関係のPermission";

    /**
     * 指定した稼働依頼プランが、指定したドライバーで稼働できるか判定
     */
    public function testCheck()
    {
        echo "\n" . __METHOD__ . "\n\n";
        
        $testConfig = new TestConfig();
        $token_path = $testConfig->getDeliveryOfficeApiTokenPath();
        $api_token = Storage::disk("private")->get($token_path);
        $headers = $testConfig->getRequestHeaders([
            "Authorization" => $api_token
        ]);


        $query_param = [
            'driver_task_plan_id' => 2,
            'driver_id' => 2,
        ];
        $query_text = "?" . http_build_query($query_param);

        $url = "/api/delivery-office/driver-task-plan-allow-driver/check" . $query_text;
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
