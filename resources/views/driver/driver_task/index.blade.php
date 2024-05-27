@extends('layouts.driver.app')

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

  @foreach ($errors->all() as $error)
    <div class="bl_msg">
      <p class="el_red">
        {{ $error }}
      </p>
    </div>

    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach


  <div class="bl_taskIndex">
    <div class="bl_taskIndex_inner">
      <form action="{{ route('driver.driver_task.index') }}" method="GET">
        <input type="hidden" name="who" value="{{ ($_GET['who'] ?? '') === 'myself' ? 'myself' : '' }}">
        <div class="bl_taskIndex_inner_head">
          <div class="bl_taskIndex_inner_head_ttl">
            <h2>{{ ($_GET['who'] ?? '') === 'myself' ? 'My' : '' }}稼働依頼一覧<span>/
                {{ ($_GET['who'] ?? '') === 'myself' ? 'my' : '' }} task list</span></h2>
          </div>
          <div class="bl_taskIndex_inner_head_keyword">
            <input type="text" name='keyword' id="keyword" placeholder="キーワード エリア"
              value={{ old('keyword', $_GET['keyword'] ?? '') }}>
          </div>

          <div class="bl_taskIndex_inner_head_orderby">
            <select name="orderby" id="orderby">
              <option disabled selected>
                並び順
              </option>
              @foreach ($orderby_list as $orderby)
                <option value={{ $orderby['value'] ?? '' }}
                  {{ old('orderby', $_GET['orderby'] ?? '') == $orderby['value'] ? 'selected' : '' }}>
                  {{ $orderby['text'] }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="bl_taskIndex_inner_head_submit">
            <input type="submit" value="検索">
          </div>

          <div class="bl_taskIndex_inner_head_filter">

            <div class="bl_taskIndex_inner_head_filter_selectbox">
              <select name="addr1_id" id="addr1_id">
                <option disabled selected>
                  都道府県
                </option>
                <option value="">
                  指定なし
                </option>
                @foreach ($prefecture_list as $prefecture)
                  <option
                    value="{{ $prefecture->id }}" {{ ($_GET['addr1_id'] ?? -1) == $prefecture->id ? 'selected' : '' }}>
                    {{ $prefecture->name }}
                  </option>
                @endforeach
              </select>
            </div>

            @if (isset($_GET['who']) && $_GET['who'] === 'myself')
              <div class="bl_taskIndex_inner_head_filter_checkbox">
                <input type="checkbox" name="task_status_id[]" value="2" id="task_status_id_2"
                  {{ ($_GET['task_status_id'] ?? false) && in_array(2, $_GET['task_status_id']) ? 'checked' : '' }}><label
                  for="task_status_id_2">新規(指名)</label>
              </div>
              <div class="bl_taskIndex_inner_head_filter_checkbox">
                <input type="checkbox" name="task_status_id[]" value="3" id="task_status_id_3"
                  {{ ($_GET['task_status_id'] ?? false) && in_array(3, $_GET['task_status_id']) ? 'checked' : '' }}><label
                  for="task_status_id_3">受諾</label>
              </div>
            @else
              <div class="bl_taskIndex_inner_head_filter_checkbox">
                <input type="checkbox" name="task_status_id[]" value="1" id="task_status_id_1"
                  {{ ($_GET['task_status_id'] ?? false) && in_array(1, $_GET['task_status_id']) ? 'checked' : '' }}><label for="task_status_id_1">新規のみ</label>
              </div>
            @endif
          </div>

        </div>
      </form>

      <div class="bl_taskIndex_inner_content">
        <ul class="bl_taskIndex_inner_content_head">
          <li>状況</li>
          <li>稼働日</li>
          <li>依頼者</li>
          <li>集荷先</li>
          <li>ドライバー運賃</li>
          <li>プラン</li>
          <li></li>
        </ul>
        <ul class="bl_taskIndex_inner_content_body">
          @foreach ($task_list as $key => $task)
            <li>
              <a @if (checkDriverAccessFilter(route('driver.driver_task.show', [
                          'task_id' => $task->id,
                      ]),
                      $is_dedicated_page = true)) href="{{ route('driver.driver_task.show', [
                          'task_id' => $task->id,
                      ]) }}" @endif>
                <div class="el_status_{{ $task->driver_task_status_id ?? '' }} el_bold">{{ $task->joinTaskStatus->name ?? '' }}
                </div>
                <div>{{ $task->taskDateYmd ?? '' }}</div>
                <div>{{ $task->joinOffice->joinCompany->name ?? ($task->joinOffice->delivery_company_name ?? '') }} {{ $task->joinOffice->name ?? '' }}</div>
                <div>
                  {{ $task->task_delivery_company_name ?? '' }} {{ $task->task_delivery_office_name ?? '' }}
                </div>

                <div>
                  {{ $task->freight_cost !== null ? number_format($task->freight_cost) : '-' }}円
                </div>

                <div>
                  {{ $task->joinDriverTaskPlan->name ?? 'No Plan' }}
                </div>

                <div class="el_form">
                  @if (
                      $task->driver_task_permission['update']['accept'] &&
                          checkDriverAccessFilter(route('driver.driver_task.update', [
                                  'task_id' => $task->id,
                                  'type' => 'accept',
                              ])))
                    <form method="POST"
                      action="{{ route('driver.driver_task.update', [
                          'task_id' => $task->id,
                          'type' => 'accept',
                      ]) }}"
                      class="js_confirm" data-confirm_msg='この依頼を引き受けます。本当によろしいですか？' data-idx="{{$key}}">
                      @csrf
                      <input type="hidden" name="type" value="accept">
                      <input type="submit" value="受諾">
                    </form>
                  @endif
                </div>
              </a>

              <div class="el_form_popup" id="el_form_popup_{{$key}}">
                <p class="el_form_popup_heading">受諾前の確認</p>

                <p class="el_form_popup_question">この依頼を引き受けます。本当によろしいですか?</p>
                <p class="el_form_popup_warning">受諾後のキャンセルは如何なる理由があっても禁止となります。</p>

                <div class="el_form_popup_checkbox">
                  <input 
                    type="checkbox"
                    name="el_form_popup_checkbox"
                    value="1"
                    id="el_form_popup_input_{{$key}}"
                  >
                  <label for="el_form_popup_input_{{$key}}">確認しました</label>
                </div>
                <div class="el_form_popup_footer">
                  <button class="el_form_popup_footer_cancel el_form_popup_footer_cancel_{{$key}}">キャンセル</button>
                  <button class="el_form_popup_footer_ok el_form_popup_footer_ok_{{$key}}">OK</button>
                </div>
              </div>
            </li>
          @endforeach
        </ul>
      </div>
      {{ $task_list->links('parts.pagination') }}
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('./js/libs/Functions/form_submit.js') }}"></script>
@endsection
