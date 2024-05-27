<?php

declare(strict_types=1);

use App\Libs\Driver\DriverAccessFilter;

if (!function_exists('checkAccessFilter')) {
    /**
     * ドライバーユーザーが対象のURLにアクセス権限があるか判定
     * 
     * @param string $route_url 対象となるURL
     * @param bool $is_dedicated_page 専用ページかどうかのフラグ
     * @return bool
     */
    function checkDriverAccessFilter($route_url, bool $is_dedicated_page = false): bool
    {
        // URLのパス部分のみを取得
        $pathOnly = ltrim(parse_url($route_url, PHP_URL_PATH), '/');

        $is_access = DriverAccessFilter::checkTargetUrl($pathOnly);

        if ($is_dedicated_page) {
            $is_access = true;
        }
        return $is_access;
    }
}
