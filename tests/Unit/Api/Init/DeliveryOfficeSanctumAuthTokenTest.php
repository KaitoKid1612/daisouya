<?php

namespace Tests\Unit\Api\Init;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestConfig;



class DeliveryOfficeSanctumAuthTokenTest extends TestCase
{
    /**
     * APIトークン作成
     * 正常値テスト
     */
    public function testStore()
    {
        echo "\n" . __METHOD__ . "\n\n";

        $data = [
            "email" => "t2@x.com",
            "password" => "test1234",
            "device_name" => "unitのiPhone"
        ];

        $testConfig = new TestConfig();

        $headers = $testConfig->getRequestHeaders();

        $response = $this->postJson('/api/delivery-office/token/store', $data, $headers);
        $response->assertStatus(200);

        /* APIトークンを退避させる処理 */
        // ディレクトリが存在しない場合、ディレクトリを作成
        $directory = "unit_test";
        if (!Storage::disk('private')->exists($directory)) {
            Storage::disk('private')->makeDirectory($directory);
        }

        $token_path = $testConfig->getDeliveryOfficeApiTokenPath();

        $api_token = "Bearer " . json_decode($response->getContent());

        Storage::disk('private')->put($token_path, $api_token);
    }

    /**
     * @depends testStore
     * APIトークン削除
     * 正常値テスト
     */
    // public function testDestroy()
    // {
    //     $file_path = base_path('tests/Unit/Api/DeliveryOffice/api_token.txt');
    //     $api_token = file_get_contents($file_path);

    //     $headers = [
    //         'Content-Type' => 'application/json',
    //         'Accept' => 'application/json',
    //         'Authorization' => $api_token,
    //     ];


    //     $response = $this->postJson('/api/delivery-office/token/destroy', [], $headers);

    //     $response->assertStatus(200);

    //     $response_data = json_decode($response->getContent(), true);
    //     $this->assertSame(true, $response_data["status"]);
    // }
}
