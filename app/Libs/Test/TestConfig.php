<?php

namespace App\Libs\Test;

/**
 * テストの定数などを設定する自作クラス
 */
class TestConfig
{
    private $deliveryOfficeApiTokenPath = 'unit_test/api_token_delivery_office.txt'; // 依頼者のAPIトークン設置パス

    private $driverApiTokenPath = 'unit_test/api_token_driver.txt'; // ドライバーのAPIトークン設置パス

    // リクエストヘッダー
    private $requestHeaders = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ];

    /**
     * 依頼者のAPIトークンのパス取得
     */
    public function getDeliveryOfficeApiTokenPath()
    {
        return $this->deliveryOfficeApiTokenPath;
    }

    /**
     * ドライバーのAPIトークンのパス取得
     */
    public function getDriverApiTokenPath()
    {
        return $this->driverApiTokenPath;
    }

    /**
     * APIのリクエストヘッダーを設定&取得
     */
    public function getRequestHeaders(array $addList = [])
    {
        $list =  $this->requestHeaders;
        $result = array_merge($list, $addList);
        return $result;
    }
}
