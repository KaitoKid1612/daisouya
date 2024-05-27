<?php

namespace App\Libs\Server;

/**
 * サーバーの解析を担当
 */
class Analysis
{
    /**
     * アクセスユーザのIPアドレスを取得
     * ロードバランサー対応。
     */
    public static function getClientIpAddress() :string
    {
        $ip = '';
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $xForwardedFor = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if (!empty($xForwardedFor)) {
                $ip = trim($xForwardedFor[0]);
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = (string)$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}
