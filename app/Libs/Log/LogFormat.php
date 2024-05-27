<?php

namespace App\Libs\Log;

use Illuminate\Support\Facades\Log;

class LogFormat
{
    /**
     * 例外内容をフォーマットしてログ出力
     *
     * @param string $msg メッセージ
     * @param string $user_type ユーザーの種類
     * @param string $login_id ログインID
     * @param string $remote_addr IPアドレス
     * @param string $http_user_agent ユーザーの端末
     * @param string $url 実行URL
     * @param string $file_path 実行ファイル
     * @param string $e_code 例外コード
     * @param string $e_file 例外が作られたファイル名
     * @param string $e_line 例外が作られた行
     * @param string $e_toString 例外文字列
     * @return string ログ出力テキスト
     */
    public static function error(string $msg = '', string $user_type = '', string $login_id = '', string $remote_addr = '', string $http_user_agent = '', string $url = '', string $file_path = '', string $e_code = '', string $e_file = '', string $e_line = '', string $e_toString = ''): string
    {
        $text = "Message:{$msg}\nユーザータイプ:{$user_type}\nログインID:{$login_id}\nREMOTE_ADDR:{$remote_addr}\nHTTP_USER_AGENT:{$http_user_agent}\n実行URL:{$url}\n実行ファイル:{$file_path}\n例外コード:{$e_code}\n例外発生ファイル:{$e_file} 行:{$e_line}\n例外文字列:{$e_toString}" . PHP_EOL;

        return $text;
    }
}
