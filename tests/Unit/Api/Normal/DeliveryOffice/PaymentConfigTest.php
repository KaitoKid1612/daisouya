<?php

namespace Tests\Unit\Api\Normal\DeliveryOffice;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestDoc;
use App\Libs\Test\TestConfig;

/**
 * 決済設定 ユニットテスト
 * @todo 決済 CUDのテスト
 */
class PaymentConfigTest extends TestCase
{
    private $section = "決済設定";

    /**
     * 一覧
     * 正常値
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

        $url = "/api/delivery-office/user/payment-config";
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
     *  Customer 取得
     * 正常値
     */
    public function testShowCustomer()
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

        $url = "/api/delivery-office/user/payment-config/customer/show" . $query_text;
        $response = $this->getJson($url, $headers);
        $response->assertStatus(200);

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
     * 支払い方法 作成
     * 正常値テスト
     */
    public function testStorePaymentMethod()
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
            "number" => "4242424242424242",
            "exp_month" => "12",
            "exp_year" => "30",
            "cvc" => "123",
        ];

        $url = "/api/delivery-office/user/payment-config/payment-method/store" . $query_text;
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
