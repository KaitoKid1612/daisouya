@extends('layouts.driver.app')

@section('title')
  スケジュール
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  <div class="bl_scheduleIndex">
    <div class="bl_scheduleIndex_inner">
      <div class="bl_scheduleIndex_inner_head">
        <div class="bl_scheduleIndex_inner_head_ttl">
          <h2>スケジュール<span>/ schedule</span></h2>
        </div>
      </div>
      <div class="bl_scheduleIndex_inner_content">
        <div class="c_calendar">
          <table>
            <thead>
              <tr>
                <th><a href="{{ route('driver.driver_schedule.index', ['calendar_month' => $calendar->prev]) }}">&lt;</a>
                </th>
                <th><a href="{{ route('driver.driver_schedule.index', ['calendar_month' => $calendar->next]) }}">&gt;</a>
                </th>
                <th colspan="3">{{ $calendar->year_month }}</th>
                <th colspan="2"><a href="{{ route('driver.driver_schedule.index') }}">Today</a></th>
              </tr>

            </thead>
            <tbody class="active">
              <tr class='el_day_week'>
                <td>Sun</td>
                <td>Mon</td>
                <td>Tue</td>
                <td>Wed</td>
                <td>Thu</td>
                <td>Fri</td>
                <td>Sat</td>
              </tr>
              {{ $calendar->show() }}
            </tbody>
          </table>
        </div>
        <div class="bl_scheduleIndex_inner_content_link">
          <a href="{{ route('driver.driver_schedule.create') }}">稼働日登録</a>
        </div>
      </div>
    </div>
  </div>

  {{-- 稼働可能日削除用フォーム --}}
  <form method="POST" id='js_form_available_date'>
    @csrf
  </form>

@endsection


@section('script_bottom')
  <script>
    /* 稼働可能日削除処理 */
    document.addEventListener('DOMContentLoaded', function() {
      let $form_available_date = document.getElementById('js_form_available_date'); // 削除用フォーム
      let $available_date_list = document.querySelectorAll('.js_available_date'); // 稼働可能日リスト

      // 稼働可能日をクリックしたら
      $available_date_list.forEach(($available_date) => {
        $available_date.addEventListener('click', (e) => {
          e.preventDefault();
          let result = window.confirm(`${$available_date.dataset.available_date} を稼働可能日から削除しますか？`);
          if (result) {
            $form_available_date.action = $available_date.dataset.action // form のactionを設定
            $form_available_date.submit();
          }
        })
        console.log('稼働可能日削除処理');
      });
    });
  </script>
@endsection
