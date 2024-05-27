<?php

namespace Tests\Unit\Api\Normal\DeliveryOffice;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestDoc;
use App\Libs\Test\TestConfig;

/**
 * 依頼者ユーザー ユニットテスト
 */
class DeliveryOfficeTest extends TestCase
{
    private $section = "依頼者ユーザー";

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

        $query_param = [];
        $query_text = "?" . http_build_query($query_param);

        $url = "/api/delivery-office/user" . $query_text;
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
     * 更新
     * 正常値テスト
     */
    public function testUpdate()
    {
        echo "\n" . __METHOD__ . "\n\n";

        $testConfig = new TestConfig();

        $token_path = $testConfig->getDeliveryOfficeApiTokenPath();
        $api_token = Storage::disk("private")->get($token_path);

        $headers = $testConfig->getRequestHeaders([
            "Authorization" => $api_token
        ]);

        $request_body = [
            'name' => "PHPUnit",
            'manager_name_sei' => "山田",
            'manager_name_mei' => "太郎",
            'manager_name_sei_kana' => "ヤマダ",
            'manager_name_mei_kana' => "タロウ",
            'delivery_company_id' => 2,
            'delivery_company_name' => "",
            'post_code1' => "110",
            'post_code2' => "0001",
            'addr1_id' => 13,
            'addr2' => "渋谷区",
            'addr3' => "千代田区1-1-1",
            'addr4' => "宮殿302",
            'manager_tel' => "0120123456",
        ];

        $query_param = [
            'type' => 'user',
        ];
        $query_text = "?" . http_build_query($query_param);

        $url = "/api/delivery-office/user/update" . $query_text;
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
