<?php

namespace App\Libs\Driver;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/**
 * ドライバーのアクセスフィルター
 */
class DriverAccessFilter
{
    /**
     * 現在アクセスしようとしているURLのアクセス権限の判定
     *
     * @return bool アクセス権限がある場合はtrue、ない場合はfalse
     */
    public static function checkCurrentUrl(): bool
    {
        $path = request()->path(); // 現在のパス
        $is_access = self::checkAccessUrl($path);

        return $is_access;
    }

    /**
     * 対象のURLのアクセス権限の判定
     *
     * @return bool アクセス権限がある場合はtrue、ない場合はfalse
     */
    public static function checkTargetUrl(String $path): bool
    {
        // URLのパス部分のみを取得
        $path_only = ltrim(parse_url($path, PHP_URL_PATH), '/');

        $is_access = DriverAccessFilter::checkAccessUrl($path_only);

        return $is_access;
    }

    private static function checkAccessUrl($path = '')
    {
        $login_id = auth('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }


        $is_access = false; // アクセス許可
        if ($login_user && in_array($login_user->driver_entry_status_id, [null, 1])){
            $is_access = true;
        }elseif ($login_user && $login_user->driver_entry_status_id == 2) {
            /* 審査中の場合 */
            // $match_pattern_list = [
            //     1 => ['name' => '完全一致'],
            //     2 => ['name' => '部分一致'],
            //     3 => ['name' => '前方一致'],
            //     4 => ['name' => '後方一致'],
            // ];

            // アクセス許可のリスト
            $allow_path_list = config('constants.DRIVER_WAITING_ALLOW_PATH_LIST');

            // 現在のパスが許可されているか判定
            foreach ($allow_path_list as $item) {
                $preg_path = preg_quote($item['path'], '/');
                $pattern_type = $item['pattern_type'];

                $pattern = '';

                if ($pattern_type == 1) {
                    $pattern = "/^{$preg_path}$/";
                } elseif ($pattern_type == 2) {
                    $pattern = "/{$preg_path}/";
                } elseif ($pattern_type == 3) {
                    $pattern = "/^{$preg_path}/";
                } elseif ($pattern_type == 4) {
                    $pattern = "/{$preg_path}$/";
                }

                if (preg_match($pattern, $path)) {
                    $is_access = true;
                    break;
                }
            }
        }

        return $is_access;
    }
}
