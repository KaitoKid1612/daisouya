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

  @if ($redis_item)
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
              <dt>data</dt>
              <dd class="el_scroll">{{ print_r($redis_item['data'] ?? '', true) }}</dd>
            </dl>

            <dl>
              <dt>id</dt>
              <dd>{{ $redis_item['id'] ?? '' }}</dd>
            </dl>

            <dl>
              <dt>uuid</dt>
              <dd>{{ $redis_item['uuid'] ?? '' }}</dd>
            </dl>

            <dl>
              <dt>attempts</dt>
              <dd>{{ $redis_item['attempts'] ?? '' }}</dd>
            </dl>

            <dl>
              <dt>displayName</dt>
              <dd>{{ $redis_item['displayName'] ?? '' }}</dd>
            </dl>

            <dl>
              <dt>job</dt>
              <dd>{{ $redis_item['job'] ?? '' }}</dd>
            </dl>


            <dl>
              <dt>maxTries</dt>
              <dd>{{ $redis_item['maxTries'] ?? '' }}</dd>
            </dl>

            <dl>
              <dt>maxExceptions</dt>
              <dd>{{ $redis_item['maxExceptions'] ?? '' }}</dd>
            </dl>


            <dl>
              <dt>failOnTimeout</dt>
              <dd>{{ $redis_item['failOnTimeout'] ?? '' }}</dd>
            </dl>

            <dl>
              <dt>backoff</dt>
              <dd>{{ $redis_item['backoff'] ?? '' }}</dd>
            </dl>

            <dl>
              <dt>timeout</dt>
              <dd>{{ $redis_item['timeout'] ?? '' }}</dd>
            </dl>

            <dl>
              <dt>retryUntil</dt>
              <dd>{{ $redis_item['retryUntil'] ?? '' }}</dd>
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
