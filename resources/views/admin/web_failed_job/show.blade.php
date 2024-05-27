@extends('layouts.admin.app')

@section('title')
  Redis 詳細
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  @if ($failed_job_item)
    <div class="bl_show">
      <div class="bl_show_inner">
        <div class="bl_show_inner_head">
          <div class="bl_show_inner_head_ttl">
            <h2>Redis 詳細</h2>
          </div>
        </div>

        <div class="bl_show_inner_content">
          <section class="bl_show_inner_content_data">

            <dl>
              <dt>exception</dt>
              <dd class="el_scroll">{{ print_r($failed_job_item->exception ?? '' , true)}}</dd>
            </dl>

            <dl>
              <dt>payload</dt>
              <dd class="el_scroll">{{ print_r($failed_job_item->payload ?? '' , true)}}</dd>
            </dl>

            <dl>
              <dt>id</dt>
              <dd>{{ $failed_job_item->id ?? '' }}</dd>
            </dl>

            <dl>
              <dt>uuid</dt>
              <dd>{{ $failed_job_item->uuid ?? '' }}</dd>
            </dl>

            <dl>
              <dt>connection</dt>
              <dd>{{ $failed_job_item->connection ?? '' }}</dd>
            </dl>

            <dl>
              <dt>queue</dt>
              <dd>{{ $failed_job_item->queue ?? '' }}</dd>
            </dl>
            
            <dl>
              <dt>failed_at</dt>
              <dd>{{ $failed_job_item->failed_at ?? '' }}</dd>
            </dl>
          </section>
        </div>
      </div>
    </div>
  @else
    <section class="bl_noData">
      <div class="bl_noData_inner">
        <p>
          このページは存在しません。
        </p>
      </div>
    </section>
  @endif
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
