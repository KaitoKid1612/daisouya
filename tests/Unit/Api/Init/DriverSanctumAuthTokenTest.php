<?php

namespace Tests\Unit\Api\init;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use App\Libs\Test\TestConfig;

class DriverSanctumAuthTokenTest extends TestCase
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

        $response = $this->postJson('/api/driver/token/store', $data, $headers);
        $response->assertStatus(200);

        /* APIトークンを退避させる処理 */
        // ディレクトリが存在しない場合、ディレクトリを作成
        $directory = "unit_test";
        if (!Storage::disk('private')->exists($directory)) {
            Storage::disk('private')->makeDirectory($directory);
        }

        $token_path = $testConfig->getDriverApiTokenPath();

        $api_token = "Bearer " . json_decode($response->getContent());

        Storage::disk('private')->put($token_path, $api_token);
    }
}
