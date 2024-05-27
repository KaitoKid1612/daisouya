<?php

namespace App\Http\Controllers\Other;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * ファイルを扱う
 */
class FileController extends Controller
{

    /**
     * ファイルを返す
     */
    public function show($path = '', Request $request)
    {
        $type = $request->type;

        $file = '';
        $mime_type = '';
        // 画像が空の場合は代わりの画像を表示する
        if (!$path) {
            if ($type === 'user_icon') {
                $path = 'images/common/dummy_user_icon.png'; // ユーザーアイコンが空の場合の画像
            } else {
                $path = 'images/common/no-data.jpg';
            }

            $public_path = public_path($path);
            if (file_exists($public_path)) {
                $file = file_get_contents($public_path);
                $mime_type = mime_content_type($public_path);
            } else {
                abort(404, 'Image not found');
            }
        } else {
            try {
                $file = Storage::disk('s3')->get($path);
                $mime_type = Storage::disk('s3')->mimeType($path);
            } catch (\Exception $e) {
                $file = '';
                $mime_type = '';
            }

            if (!$file) {
                try {
                    $file = Storage::disk('public')->get($path);
                    $mime_type = Storage::disk('public')->mimeType($path);
                } catch (\Throwable $e) {
                    $file = '';
                    $mime_type = '';
                }
            }

            if(!$file) {
                abort(404, 'Image not found');
            }


        }

        return response($file, 200)->header('Content-Type', $mime_type);
    }
}
