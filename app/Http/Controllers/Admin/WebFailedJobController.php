<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FailedJob;

class WebFailedJobController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $failed_job_list = FailedJob::select()->get();

        return view('admin.web_failed_job.index', [
            'failed_job_list' => $failed_job_list,
        ]);
    }

    /**
     * 詳細画面
     *
     * @param  int  $failed_job_id
     * @return \Illuminate\Http\Response
     */
    public function show($failed_job_id)
    {
        $failed_job_item = FailedJob::select()->where('id', $failed_job_id)->first();

        return view('admin.web_failed_job.show', [
            'failed_job_item' => $failed_job_item,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $failed_job_id
     * @return \Illuminate\Http\Response
     */
    public function edit($failed_job_id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $failed_job_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $failed_job_id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $failed_job_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($failed_job_id)
    {
        //
    }
}
