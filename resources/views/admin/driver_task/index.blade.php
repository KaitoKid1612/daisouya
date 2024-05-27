@extends('layouts.admin.app')

@section('title')
  稼働依頼一覧
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
          <h2>稼働依頼一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        <div class="bl_index_inner_content_handle">
          <a href="{{ route('admin.driver_task.create') }}" class="c_btn bl_index_inner_content_handle_item">作成</a>
          <a href="{{ route('admin.driver_task.export.index') }}"
            class="c_btn bl_index_inner_content_handle_item">エクスポート</a>
        </div>

        <section class="bl_index_inner_content_formBox">
          <button class="js_show_form_btn bl_index_inner_content_formBox_button">検索フォーム</button>
          <form action="{{ route('admin.driver_task.index') }}" method="GET" class="js_form">
            <input type="hidden" name="driver_id" value="{{ $_GET['driver_id'] ?? '' }}">
            <input type="hidden" name="delivery_office_id" value="{{ $_GET['delivery_office_id'] ?? '' }}">


            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="keyword">検索ワード</label>
              <input type="text" name='keyword' id="keyword" value="{{ $_GET['keyword'] ?? '' }}" class="el_max_width45rem">
            </div>

            <div class="c_form_submit bl_index_inner_content_formBox_item">
              <input type="submit" value='検索'>
            </div>

            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_task_date">稼働日</label>
              <input type="date" name='from_task_date' id="from_task_date"
                value="{{ $_GET['from_task_date'] ?? '' }}" class='el_width12rem'>
              <span>-</span>
              <input type="date" name='to_task_date' id="to_task_date"
                value="{{ $_GET['to_task_date'] ?? '' }}" class='el_width12rem'>
            </div>

            <div class='bl_index_inner_content_formBox_list'>
              <label class='bl_index_inner_content_formBox_list_ttl'>ステータス</label>
              <ul>
                @foreach ($task_status_list as $task_status)
                  <li class='c_form_checkbox'>
                    <input type="checkbox"
                      name='task_status_id[]'
                      value='{{ $task_status['id'] }}'
                      id='task_status_id{{ $task_status['id'] }}'
                      {{ isset($_GET['task_status_id']) && in_array($task_status['id'], $_GET['task_status_id']) ? 'checked' : '' }}>
                    <label
                      for="task_status_id{{ $task_status['id'] }}">{{ $task_status['name'] }}
                    </label>
                  </li>
                @endforeach
              </ul>
            </div>


            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_created_at">作成日</label>
              <input type="date" name='from_created_at' id="from_created_at"
                value="{{ $_GET['from_created_at'] ?? '' }}" class='el_width12rem'>
              <span>-</span>
              <input type="date" name='to_created_at'
                value="{{ $_GET['to_created_at'] ?? '' }}" class='el_width12rem'>
            </div>


            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_updated_at">更新日</label>
              <input type="date" name='from_updated_at' id="from_updated_at"
                value="{{ $_GET['from_updated_at'] ?? '' }}" class='el_width12rem'>
              <span>-</span>

              <input type="date" name='to_updated_at'
                value="{{ $_GET['to_updated_at'] ?? '' }}" class='el_width12rem'>
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
                <th class='el_width4rem'>ID</th>
                <th class='el_width6rem'>稼働日</th>
                <th class='el_width6rem'>稼働依頼プラン</th>
                <th class='el_width8rem'>ドライバー名</th>
                <th class='el_width10rem'>配送営業所(依頼者)</th>
                <th class='el_width6rem'>ステータス</th>
                <th class='el_width7rem'>合計</th>
                <th class='el_width7rem'>システム利用料金</th>
                <th class='el_width7rem'>システム利用料金(繁忙期)</th>
                <th class='el_width7rem'>ドライバー運賃</th>
                <th class='el_width7rem'>緊急依頼料金</th>
                <th class='el_width7rem'>税金</th>
                <th class='el_width7rem'>値引き額</th>
                <th class='el_width7rem'>返金額</th>
                <th class='el_width7rem'>支払いステータス</th>
                <th class='el_width7rem'>返金ステータス</th>
                <th class='el_width11rem'>作成日</th>
                <th class='el_width4rem'>編集</th>
              </tr>

              @foreach ($task_list as $task)
                <tr>
                  <td class='el_center'><a href="{{ route('admin.driver_task.show', ['task_id' => $task->id]) }}"
                      class='c_link el_btn'>{{ $task->id }}</a>
                  </td>
                  <td class='el_center'>{{ $task->taskDateYmd ?? '' }}</td>
                  <td class='el_center'>{{ $task->joinDriverTaskPlan->name ?? 'No Plan' }}</td>
                  <td>
                    @if ($task->driver_id && $task->joinDriver)
                      <a href="{{ route('admin.driver.show', ['driver_id' => $task->driver_id]) }}" class="c_normal_link">
                        {{ $task->joinDriver->full_name ?? '' }}
                      </a>
                    @elseif($task->driver_id && !$task->joinDriver)
                      <a href="{{ route('admin.driver.show', ['driver_id' => $task->driver_id]) }}" class="c_normal_link">
                        {{ $task->joinDriver->full_name ?? 'データなしorソフト削除済み' }}
                      </a>
                    @else
                      なし
                    @endif
                  </td>
                  <td>
                    @if ($task->delivery_office_id && $task->joinOffice)
                      <a href="{{ route('admin.delivery_office.show', ['office_id' => $task->delivery_office_id]) }}" class="c_normal_link">
                        {{ $task->joinOffice->name ?? '' }}
                      </a>
                    @elseif($task->delivery_office_id && !$task->joinOffice)
                      <a href="{{ route('admin.delivery_office.show', ['office_id' => $task->delivery_office_id]) }}" class="c_normal_link">
                        {{ $task->joinOffice->name ?? 'データなしorソフト削除済み' }}
                      </a>
                    @else
                      なし
                    @endif
                  </td>
                  <td class='el_center'>{{ $task->joinTaskStatus->name ?? '' }}</td>
                  <td class='el_center'>{{ number_format($task->total_price ?? 0) }}</td>
                  <td class='el_center'>{{ number_format($task->system_price ?? 0) }}</td>
                  <td class='el_center'>{{ number_format($task->busy_system_price ?? 0) }}</td>
                  <td class='el_center'>{{ $task->freight_cost !== null ? number_format($task->freight_cost) : '-' }}</td>
                  <td class='el_center'>{{ number_format($task->emergency_price ?? 0) }}</td>
                  <td class='el_center'>{{ number_format($task->tax ?? 0) }}</td>
                  <td class='el_center'>{{ number_format($task->discount ?? 0) }}</td>
                  <td class='el_center'>{{ number_format($task->refund_amount ?? 0) }}</td>
                    <td class='el_center'>
                        @php
                            $paymentStatus = $task->joinTaskPaymentStatus->name ?? '';
                            $isInStatusArray = in_array($task->driver_task_status_id, [1, 2, 5, 6, 7, 9, 10]);
                        @endphp

                        @if($task->joinOffice->charge_user_type_id === 1)
                            {{ !$isInStatusArray ? '支払い済み' : $paymentStatus }}
                        @elseif($task->joinOffice->charge_user_type_id === 2)
                            {{ !$isInStatusArray ? '請求書払い' : $paymentStatus . '【請求書払い】' }}
                        @endif
                    </td>
                    <td class='el_center'>{{ $task->joinTaskRefundStatus->name ?? '' }}</td>

                  <td>{{ $task->created_at }}</td>
                  <td class='el_center'><a href="{{ route('admin.driver_task.edit', ['task_id' => $task->id]) }}"
                      class='c_link el_btn'>編集</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $task_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
