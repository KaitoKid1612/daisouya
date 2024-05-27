@extends('layouts.admin.app')

@section('title')
  Redis
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  <div class="bl_index">
    <div class="bl_index_inner">
      <div class="bl_index_inner_head">
        <div class="bl_index_inner_head_ttl">
          <h2>Redis 一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        <div class="bl_index_inner_content_handle">
          <a href="{{ route('admin.web_redis.index') }}" class="c_btn bl_index_inner_content_handle_item">default</a>
          <a href="{{ route('admin.web_redis.index', ['type' => 'email']) }}" class="c_btn bl_index_inner_content_handle_item">email</a>
        </div>

        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class='el_width4rem'></th>
                <th class='el_width4rem'>id</th>
                <th class='el_width4rem'>uuid</th>
                <th class='el_width4rem'>attempts</th>
                <th class='el_width24rem'>displayName</th>
                <th class='el_width17rem'>job</th>
                <th class='el_width4rem'>maxTries</th>
                <th class='el_width6rem'>maxExceptions</th>
                <th class='el_width6rem'>failOnTimeout</th>
                <th class='el_width4rem'>backoff</th>
                <th class='el_width4rem'>timeout</th>
                <th class='el_width4rem'>retryUntil</th>
              </tr>

              @foreach ($redis_list as $redis_item)
                <tr>
                  <td class='el_center'><a
                      href="{{ route('admin.web_redis.show', ['redis_id' => $redis_item['uuid'], 'type' => $_GET['type'] ?? '']) }}"class='c_link el_btn'>詳細</a></td>
                  <td>{{ $redis_item['id'] ?? '' }}</td>
                  <td>{{ $redis_item['uuid'] ?? '' }}</td>
                  <td>{{ $redis_item['attempts'] ?? '' }}</td>
                  <td>{{ $redis_item['displayName'] ?? '' }}</td>
                  <td>{{ $redis_item['job'] ?? '' }}</td>
                  <td>{{ $redis_item['maxTries'] ?? '' }}</td>
                  <td>{{ $redis_item['maxExceptions'] ?? '' }}</td>
                  <td>{{ $redis_item['failOnTimeout'] ?? '' }}</td>
                  <td>{{ $redis_item['backoff'] ?? '' }}</td>
                  <td>{{ $redis_item['timeout'] ?? '' }}</td>
                  <td>{{ $redis_item['retryUntil'] ?? '' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
