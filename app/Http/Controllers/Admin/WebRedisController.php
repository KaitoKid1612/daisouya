<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

/**
 * Redis
 */
class WebRedisController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type;
        
        $queues_name = '';
        if($type == 'email') {
            $queues_name = 'queues:email';
        }else{
            $queues_name = 'queues:default';
        }

        $redis_list = Redis::command('lrange', [$queues_name, 0, -1]);

        $redis_list = array_map(function ($item) {
            return json_decode($item, true);
        }, $redis_list);

        // logger($redis_list);

        return view('admin.web_redis.index', [
            'redis_list' => $redis_list,
        ]);
    }

    /**
     * 詳細
     *
     * @param  int  $redis_id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $redis_id)
    {
        $type = $request->type;
        
        $queues_name = '';
        if($type == 'email') {
            $queues_name = 'queues:email';
        }else{
            $queues_name = 'queues:default';
        }

        $redis_list = Redis::command('lrange', [$queues_name, 0, -1]);
        
        $redis_item = '';
        foreach ($redis_list as $item) {
            $item = json_decode($item, true);
            if($item["uuid"] == $redis_id) {
                $redis_item = $item;
                break;
            }
        }

        return view('admin.web_redis.show', [
            'redis_item' => $redis_item,
        ]);
    }
}
