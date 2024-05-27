@extends('layouts.admin.app')

@section('title')
  稼働エクスポート
@endsection

@section('content')
  <div class="bl_export">
    <div class="bl_export_inner">
      <div class="bl_export_inner_head">
        <div class="bl_export_inner_head_ttl">
          <h2>稼働エクスポート<span>/ task export</span></h2>
        </div>
      </div>
      <div class="bl_export_inner_content">
        <form method="POST" action="{{ route('admin.driver_task.export.read') }}">
          @csrf
          <div class="bl_export_inner_content_form">
            <div class="bl_export_inner_content_form_item el_select">
              <label for="orderby">並び順</label>
              <select name="orderby" id="orderby">
                <option disabled selected>
                  並び順
                </option>
                <option value=''>
                  指定なし
                </option>
                @foreach ($orderby_list as $orderby)
                  <option value={{ $orderby['value'] }}
                    {{ old('orderby', $_GET['orderby'] ?? '') === $orderby['value'] ? 'selected' : '' }}>
                    {{ $orderby['text'] }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="bl_export_inner_content_form_item">
              <label for="from_task_date">稼働日 範囲</label>
              <input type="date" name='from_task_date' id="from_task_date"
                value={{ old('from_task_date', $_GET['from_task_date'] ?? '') }} id='from_task_date' class='el_width12rem '>

              <span>-</span>

              <input type="date" name='to_task_date' id="to_task_date"
                value={{ old('to_task_date', $_GET['to_task_date'] ?? '') }} id='to_task_date' class='el_width12rem '>
            </div>


            <div class="bl_export_inner_content_form_caption">
              <h3>
                稼働ステータス
              </h3>
            </div>
            <div class="bl_export_inner_content_form_list">
              <div class="bl_export_inner_content_form_list_box">
                <div class="bl_export_inner_content_form_list_box_handle">
                  <button type="button" class="js_check_btn_task_status_id c_btn">全選択</button>
                  <button type="button" class="js_uncheck_btn_task_status_id c_btn">全解除</button>
                </div>
                <ul>
                  @foreach ($task_status_list as $task_status)
                    <li class='c_form_checkbox'>
                      <input type="checkbox"
                        name='task_status_id[]'
                        value='{{ $task_status['id'] }}'
                        id='task_status_id{{ $task_status['id'] }}'>
                      <label
                        for="task_status_id{{ $task_status['id'] }}">{{ $task_status['name'] }}
                      </label>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>



            <div class="bl_export_inner_content_form_caption">
              <h3>
                営業所選択
              </h3>
              <aside>※特定の営業所で絞り込みたい場合は選択してください。</aside>
            </div>
            <section class="bl_export_inner_content_form_list">
              @foreach ($delivery_multi_list as $delivery_list)
                <div class="bl_export_inner_content_form_list_box js_check_list_box">
                  <h4 class='bl_export_inner_content_form_list_box_ttl'>
                    {{ $delivery_list['company']['name'] }}
                  </h4>
                  <div class="bl_export_inner_content_form_list_box_handle">
                    <button type="button" class="js_check_btn_delivery_office_id c_btn"
                      data-company_id="{{ $delivery_list['company']['id'] }}">全選択</button>
                    <button type="button" class="js_uncheck_btn_delivery_office_id c_btn"
                      data-company-id="{{ $delivery_list['company']['id'] }}">全解除</button>
                  </div>
                  <ul>
                    @foreach ($delivery_list['office_list'] as $office)
                      <li class='c_form_checkbox'>
                        <input type="checkbox"
                          name='delivery_office_id[]'
                          value='{{ $office['id'] }}'
                          id='{{ $delivery_list['company']['name'] }}_{{ $office['name'] }}'
                          class="js_company_{{ $delivery_list['company']['id'] }}">
                        <label
                          for="{{ $delivery_list['company']['name'] }}_{{ $office['name'] }}">{{ $office['name'] }}
                        </label>
                      </li>
                    @endforeach
                  </ul>
                </div>
              @endforeach
            </section>
            <div class="bl_export_inner_content_form_submit">
              <input type="submit" value="エクスポート" class="c_btn">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      /* ステータスのチェックボックスの全チェック(解除) */
      window.globalFunction.check_all(
        document.querySelector('.js_check_btn_task_status_id'), // 全選択ボタン
        document.getElementsByName("task_status_id[]") // チェックボックスリスト
      );

      window.globalFunction.uncheck_all(
        document.querySelector('.js_uncheck_btn_task_status_id'), // 全解除ボタン
        document.getElementsByName("task_status_id[]") // チェックボックスリスト
      );


      /* 配送会社ごとののチェックボックスの全チェック(解除) */
      $check_list_box_list = document.querySelectorAll('.js_check_list_box'); // チェックボックスの親要素リスト

      $check_list_box_list.forEach(($check_list_box) => {
        $check_btn = $check_list_box.querySelector(".js_check_btn_delivery_office_id"); // 全選択ボタン
        $uncheck_btn = $check_list_box.querySelector(".js_uncheck_btn_delivery_office_id"); // 全解除ボタン
        $checkbox_list = $check_list_box.querySelectorAll(`.js_company_${$check_btn.dataset.company_id}`) // チェックボックスリスト

        window.globalFunction.check_all($check_btn, $checkbox_list);
        window.globalFunction.uncheck_all($uncheck_btn, $checkbox_list);
      });

    });
  </script>
@endsection
