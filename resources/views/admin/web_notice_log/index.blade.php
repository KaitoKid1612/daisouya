@extends('layouts.admin.app')

@section('title')
  通知ログ
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
          <h2>通知ログ 一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">


        <section class="bl_index_inner_content_formBox">
          <button class="js_show_form_btn bl_index_inner_content_formBox_button">検索フォーム</button>
          <form action="{{ route('admin.web_notice_log.index') }}" method="GET" class="js_form">

            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="keyword">検索ワード</label>
              <input type="text" name='keyword' id="keyword" value="{{ $_GET['keyword'] ?? '' }}" class="el_max_width45rem">
            </div>

            <div class="c_form_submit bl_index_inner_content_formBox_item">
              <input type="submit" value='検索'>
            </div>

            {{-- <div class="bl_index_inner_content_formBox_list">
              <div class="bl_index_inner_content_formBox_list_box">
                <h4 class='bl_index_inner_content_formBox_list_ttl'>
                  ユーザータイプ
                </h4>
                <ul>
                  @foreach ($web_contact_status_list as $web_contact_status)
                    <li class='c_form_checkbox'>
                      <input type="checkbox"
                        name='web_contact_status_id[]'
                        value='{{ $web_contact_status->id }}'
                        id='web_contact_status{{ $web_contact_status->id }}'
                        {{ isset($_GET['web_contact_status_id']) && in_array($web_contact_status->id, $_GET['web_contact_status_id']) ? 'checked' : '' }}>
                      <label
                        for="web_contact_status{{ $web_contact_status->id }}">{{ $web_contact_status->name ?? '' }}
                      </label>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>
             --}}

            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="driver_task_id">稼働ID</label>
              <input type="number" name='driver_task_id' id="driver_task_id" value="{{ $_GET['driver_task_id'] ?? '' }}" class="el_max_width20rem">
            </div>

            <div class="bl_index_inner_content_formBox_list">
              <div class="bl_index_inner_content_formBox_list_box">
                <h4 class='bl_index_inner_content_formBox_list_ttl'>
                  通知の種類
                </h4>
                <ul>
                  @foreach ($web_notice_type_list as $notice_type)
                    <li class='c_form_checkbox'>
                      <input type="checkbox"
                        name='notice_type_id[]'
                        value='{{ $notice_type->id }}'
                        id='notice_type_id{{ $notice_type->id }}'
                        {{ isset($_GET['notice_type_id']) && in_array($notice_type->id, $_GET['notice_type_id']) ? 'checked' : '' }}>
                      <label
                        for="notice_type_id{{ $notice_type->id }}">{{ $notice_type->name ?? '' }}
                      </label>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>

            <div class="bl_index_inner_content_formBox_list">
              <div class="bl_index_inner_content_formBox_list_box">
                <h4 class='bl_index_inner_content_formBox_list_ttl'>
                  ログ種類
                </h4>
                <ul>
                  @foreach ($web_log_level_list as $log_level)
                    <li class='c_form_checkbox'>
                      <input type="checkbox"
                        name='log_level_id[]'
                        value='{{ $log_level->id }}'
                        id='log_level_id{{ $log_level->id }}'
                        {{ isset($_GET['log_level_id']) && in_array($log_level->id, $_GET['log_level_id']) ? 'checked' : '' }}>
                      <label
                        for="log_level_id{{ $log_level->id }}">{{ $log_level->name ?? '' }}
                      </label>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>

            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_created_at">作成日</label>
              <input type="date" name='from_created_at' id="from_created_at"
                value="{{ $_GET['from_created_at'] ?? '' }}" class='el_width12rem'>
              <span>-</span>
              <input type="date" name='to_created_at'
                value="{{ $_GET['to_created_at'] ?? '' }}" class='el_width12rem'>
            </div>


            <div class="c_form_item el_width12rem bl_index_inner_content_formBox_item">
              <label for="orderby">並び順</label>
              <div class="c_form_select">
                <select name="orderby" id="orderby">
                  <option disabled selected>
                    選択してください。
                  </option>
                  <option value=''>
                    指定なし
                  </option>
                  @foreach ($orderby_list as $orderby)
                    <option value="{{ $orderby['value'] }}"
                      {{ isset($_GET['orderby']) && $_GET['orderby'] == $orderby['value'] ? 'selected' : '' }}>
                      {{ $orderby['text'] }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="c_form_submit bl_index_inner_content_formBox_item">
              <input type="submit" value='検索'>
            </div>


            <div class="c_reset_btn_box bl_index_inner_content_formBox_item">
              <button class="js_reset_form_btn">フォームリセット</button>
            </div>

          </form>
        </section>


        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class='el_width3rem'>ID</th>
                <th class='el_width5rem'>ログレベル</th>
                <th class='el_width6rem'>通知の種類</th>
                <th class='el_width6rem'>稼働ID</th>
                <th class='el_width10rem'>受信者のユーザーID</th>
                <th class='el_width12rem'>受信者の情報</th>
                <th class='el_width10rem'>通知を発火させたユーザのID</th>
                <th class='el_width12rem'>通知を発火させたユーザの情報</th>
                <th class='el_width10rem'>通知の内容</th>
                <th class='el_width20rem'>実行URL</th>
                <th class='el_width7rem'>ユーザIPアドレス</th>
                <th class='el_width50rem'>ユーザ OSブラウザ</th>
                <th class='el_width9rem'>作成日</th>
              </tr>

              @foreach ($web_notice_log_list as $web_notice_log)
                <tr>
                  <td class='el_center'>{{ $web_notice_log->id ?? '' }}</td>
                  <td>{{ $web_notice_log->joinLogLevel->name ?? '' }}</td>
                  <td>{{ $web_notice_log->joinNoticeType->name ?? '' }}</td>
                  <td class='el_center'>
                    @if ($web_notice_log->task_id)
                      <a href="{{ route('admin.driver_task.show', ['task_id' => $web_notice_log->task_id]) }}"
                        class='c_normal_link'>
                        {{ $web_notice_log->task_id ?? '' }}
                      </a>
                    @endif
                  </td>
                  <td>{{ $web_notice_log->to_user_id ?? '' }}</td>
                  <td>{{ $web_notice_log->to_user_info ?? '' }}</td>
                  <td>{{ $web_notice_log->user_id ?? '' }}</td>
                  <td>{{ $web_notice_log->user_info ?? '' }}</td>
                  <td>{{ $web_notice_log->text ?? '' }}</td>
                  <td>{{ $web_notice_log->url ?? '' }}</td>
                  <td>{{ $web_notice_log->remote_addr ?? '' }}</td>
                  <td>{{ $web_notice_log->http_user_agent ?? '' }}</td>
                  <td>{{ $web_notice_log->created_at ?? '' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $web_notice_log_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
