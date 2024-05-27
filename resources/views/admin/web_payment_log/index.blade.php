@extends('layouts.admin.app')

@section('title')
  決済ログ
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
          <h2>決済ログ 一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">


        <section class="bl_index_inner_content_formBox">
          <button class="js_show_form_btn bl_index_inner_content_formBox_button">検索フォーム</button>
          <form action="{{ route('admin.web_payment_log.index') }}" method="GET" class="js_form">

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
                  決済ステータス
                </h4>
                <ul>
                  @foreach ($web_payment_log_status_list as $payment_log_status)
                    <li class='c_form_checkbox'>
                      <input type="checkbox"
                        name='payment_log_status[]'
                        value='{{ $payment_log_status->id }}'
                        id='payment_log_status{{ $payment_log_status->id }}'
                        {{ isset($_GET['payment_log_status']) && in_array($payment_log_status->id, $_GET['payment_log_status']) ? 'checked' : '' }}>
                      <label
                        for="payment_log_status{{ $payment_log_status->id }}">{{ $payment_log_status->name ?? '' }}
                      </label>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>

            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_date">決済日</label>
              <input type="date" name='from_date' id="from_created_at"
                value="{{ $_GET['from_date'] ?? '' }}" class='el_width12rem'>
              <span>-</span>
              <input type="date" name='to_date'
                value="{{ $_GET['to_date'] ?? '' }}" class='el_width12rem'>
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
                <th class='el_width9rem'>支払い日</th>
                <th class='el_width6rem'>金額</th>
                <th class='el_width6rem'>稼働ID</th>
                <th class='el_width8rem'>支払いログステータス</th>
                <th class='el_width8rem'>支払いログ事由</th>
                <th class='el_width8rem'>メッセージ</th>
                <th class='el_width8rem'>支払いユーザID</th>
                <th class='el_width8rem'>支払いユーザの種類</th>
                <th class='el_width8rem'>受け取りユーザID</th>
                <th class='el_width8rem'>受け取りユーザの種類</th>
                <th class='el_width9rem'>作成日</th>
                <th class='el_width9rem'>更新日</th>
              </tr>

              @foreach ($web_payment_log_list as $web_payment_log)
                <tr>
                  <td class='el_center'>
                    {{ $web_payment_log->id }}
                  </td>
                  <td>{{ $web_payment_log->date }}</td>
                  <td class='el_right'>{{ $web_payment_log->amount_money ?? '' }}</td>
                  <td>
                    <a href="{{ route('admin.driver_task.show', ['task_id' => $web_payment_log->driver_task_id]) }}" class="c_normal_link">
                      {{ $web_payment_log->driver_task_id ?? '' }}
                    </a>
                  </td>
                  <td>{{ $web_payment_log->joinPaymentLogStatus->name ?? '' }}</td>
                  <td>{{ $web_payment_log->joinPaymentReason->name ?? '' }}</td>
                  <td>{{ $web_payment_log->message }}</td>
                  <td>{{ $web_payment_log->pay_user_id }}</td>
                  <td>{{ $web_payment_log->joinPayUserType->name ?? '' }}</td>
                  <td>{{ $web_payment_log->receive_user_id ?? '' }}</td>
                  <td>{{ $web_payment_log->joinReceiveUserType->name ?? '' }}</td>
                  <td>{{ $web_payment_log->created_at }}</td>
                  <td>{{ $web_payment_log->updated_at }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $web_payment_log_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
