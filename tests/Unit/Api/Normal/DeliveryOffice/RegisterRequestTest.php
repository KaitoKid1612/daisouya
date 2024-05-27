<?php

namespace Tests\Unit\Api\Normal\DeliveryOffice;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestDoc;
use App\Libs\Test\TestConfig;

use Illuminate\Support\Facades\Mail;

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
        $token_path = $testConfig->getDeliveryOfficeApiTokenPath();
        // $api_token = Storage::disk("private")->get($token_path);
        $headers = $testConfig->getRequestHeaders([
            // "Authorization" => $api_token
        ]);

        $query_param = [];
        $query_text = "?" . http_build_query($query_param);

        $request_body = [
            "register_request_status_id" => 1,
            "name" => "APIname",
            "manager_name_sei" => "Unitapi姓",
            "manager_name_mei" => "Unitapi名",
            "manager_name_sei_kana" => "テストセイ",
            "manager_name_mei_kana" => "テストメイ",
            "email" => "api4@amazon.com",
            "delivery_company_id" => 3,
            "delivery_company_name" => "api会社",
            "delivery_office_type_id" => "api type",
            "post_code1" => "123",
            "post_code2" => "9876",
            "addr1_id" => 4,
            "addr2" => "apiaddr2",
            "addr3" => "apiaddr3",
            "addr4" => "apiaddr4",
            "manager_tel" => "0987654321",
            "message" => "apimessage",
            "terms_service" => true
        ];

        $url = "/api/delivery-office/register-request/store" . $query_text;
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
