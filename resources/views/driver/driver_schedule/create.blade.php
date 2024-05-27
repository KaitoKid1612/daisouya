@extends('layouts.driver.app')

@section('title')
  稼働日登録
@endsection

@section('content')
  <div class="bl_scheduleCreate">
    <div class="bl_scheduleCreate_inner">
      <div class="bl_scheduleCreate_inner_head">
        <div class="bl_scheduleCreate_inner_head_ttl">
          <h2>稼働日登録<span>/ register schedule</span></h2>
        </div>
      </div>
      <div class="bl_scheduleCreate_inner_content">
        <p class="bl_scheduleCreate_inner_content_text">稼働できる日を選択してください。</p>
        @error('available_date')
          <p class="el_error_msg el_textAlign_center ">{{ $message }}</p>
        @enderror
        <div class="c_calendar">
          <table class='calendar'>
            <thead>
              <tr>
                <th><button id="prev" class="">&lt;</button></th>
                <th><button id="next" class="">&gt;</button></th>
                <th colspan="3" class='calendar_year_month'></th>
                <th colspan="2"><button id="today_btn">今月</button></th>
              </tr>
            </thead>
          </table>
        </div>

        <div class="bl_scheduleCreate_inner_content_form">
          <form action="{{ route('driver.driver_schedule.store') }}" method="POST" id="js_schedule_form">
            @csrf
            <div class="js_input_box">
              {{-- inputタグが入る --}}
              {{-- available_date:<input type="date" name='available_date[]' id='available_date'> --}}
            </div>
            <div class="bl_scheduleCreate_inner_content_form_submit">
              <input type="submit" value="作成" id="js_submit_schedule">
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script>
    /**
     * 選択した日付のinputを作成する
     */
    const schedule_form = document.getElementById('js_schedule_form');
    let $input_box = document.querySelector('.js_input_box')
    let submit = document.getElementById('js_submit_schedule');
    console.log($input_box);

    submit.addEventListener('click', (e) => {
      e.preventDefault();
      $input_box.innerHTML = '';
      inputDateList.forEach((item) => {
        console.log(item);
        $input_box.innerHTML += `<input type='hidden' name='available_date[]' value='${item}'>`;
      });
      if (inputDateList.length) {
        let is_confirm = window.confirm('登録して良いですか? \r\n ※すでに登録されている日を選択した場合は上書きされます。')
        if (is_confirm) {
          schedule_form.submit();
        }
      }
    });
  </script>
  <script src="{{ asset('js/libs/MyCalendar/BaseCalendar.js') }}"></script>
  <script>
    // カレンダー生成 
    let calendar_control = new CalendarControl(0, 5);


    /* 登録できる日付の制限 */
    // いつから
    let date_from_limit = new Date();
    date_from_limit.setDate(date_from_limit.getDate() - 1); // 
    console.log(date_from_limit);

    // いつまで
    let date_to_limit = new Date();
    date_to_limit.setMonth(date_to_limit.getMonth() + 3); // 
    console.log(date_to_limit);


    // input に含日付データ
    let inputDateList = [];

    // 日付一覧 要素取得
    let $calendar_td_list = document.querySelectorAll('.js_calendar_td');

    /* 日付がクリックされたときの処理 */
    $calendar_td_list.forEach(($calendar_td) => {
      $calendar_td.addEventListener("click", function(e) {
        // 有効でない日付の範囲の場合は無効
        let date_td = new Date($calendar_td.id);
        if (date_td < date_from_limit || date_td > date_to_limit) {
          // console.log('日付 無効');
          return;
        }

        // クリックされた日付の処理
        let clickDate = document.getElementById($calendar_td.id);
        if (clickDate.classList.contains("active")) {
          // 削除
          clickDate.classList.remove('active')
          var index = inputDateList.indexOf(clickDate); // インデックス番号探す
          inputDateList.splice(index, 1); // 送信するデータから除く
        } else {
          // 追加
          clickDate.classList.add('active')
          inputDateList.push($calendar_td.id); // 送信するデータに含める
        }

        // console.log(inputDateList);
        // console.log(e.target);
      });
    });

    /* 登録できない日付の処理 */
    $calendar_td_list.forEach(($item) => {
      let date_td = new Date($item.id);
      if (date_td < date_from_limit || date_td > date_to_limit) {
        $item.classList.add('disable'); // 背景をグレイにする。
        return;
      }
    });
  </script>
@endsection
