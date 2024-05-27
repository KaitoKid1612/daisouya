@extends('layouts.admin.app')

@section('title')
  スケジュール 作成
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  <div class="bl_create">
    <div class="bl_create_inner">
      <div class="bl_create_inner_head">
        <div class="bl_create_inner_head_ttl">
          <h2>スケジュール 作成 (繁忙日)</h2>
        </div>
      </div>

      <div class="bl_create_inner_content">
        <section class="bl_create_inner_content_data">
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
            <form action="{{ route('admin.web_busy_season.store') }}" method="POST" id="js_schedule_form">
              @csrf
              <div class="js_input_box">
                {{-- inputタグが入る --}}
                {{-- busy_date:<input type="date" name='busy_date[]' id='busy_date'> --}}
              </div>
              <div class=" bl_create_inner_content_data_form_item el_submit el_textAlign_center">
                <input type="submit" value="作成" id = "js_submit_schedule" class='c_btn'>
              </div>
            </form>
          </div>
        </section>
      </div>
    </div>
  </div>


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
        $input_box.innerHTML += `<input type='hidden' name='busy_date[]' value='${item}'>`;
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
    let calendar_control = new CalendarControl(0, 25);


    /* 登録できる日付の制限 */
    // いつから
    let date_from_limit = new Date();
    date_from_limit.setDate(date_from_limit.getDate() - 1); // 
    console.log(date_from_limit);

    // いつまで
    let date_to_limit = new Date();
    date_to_limit.setMonth(date_to_limit.getMonth() + 24); // 
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

@section('script_bottom')
  <script>
    let busy_date_list = [];

    (async () => {
      await getScheduleAPI();

      // 繁忙日の日のUI
      $calendar_td_list.forEach(($calendar_td) => {
        if (busy_date_list.includes($calendar_td.id)) {
          $calendar_td.classList.add('disable');
          $calendar_td.innerHTML = `${$calendar_td.innerHTML}<span class="busy_date el_black" data-busy_date="${$calendar_td.id}">繁忙</span>`;
        }
      });
    }).call(this);


    /**
     * スケジュール取得API
     */
    async function getScheduleAPI() {

      // 本日の1日から 24ヶ月後の末日なでのスケジュールを取得
      let today = new Date();
      let todayYear = today.getFullYear();
      let todayMonth = today.getMonth();
      let from_date_text = `${todayYear}-${("00" + todayMonth + 1).slice(-2)}-01`;
      let to_date = new Date(todayYear, todayMonth + 24, 0);
      console.log(`to_date:${to_date}`);
      let to_date_text = `${to_date.getFullYear()}-${("00" + (to_date.getMonth() + 1)).slice(-2)}-${("00" + to_date.getDate()).slice(-2)}`;

      await axios.get("/admin/api/base-config/busy-season", {
          params: {
            from_date: from_date_text,
            to_date: to_date_text,
          }
        })
        .then(function(res) {
          let $data = res["data"]["data"];

          $data.forEach((item) => {
            busy_date_list.push(item.busy_date);
          })
        })
        .catch(function(error) {
          console.log(error);
          console.log('バリデーション ', error.response.data.errors);
        });
    }
  </script>
@endsection
