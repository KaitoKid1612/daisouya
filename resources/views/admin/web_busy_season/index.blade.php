@extends('layouts.admin.app')

@section('title')
  スケジュール一覧
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
          <h2>スケジュール 一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        <div class="bl_index_inner_content_handle">
          <a href="{{ route('admin.web_busy_season.create') }}" class="c_btn bl_index_inner_content_handle_item">繁忙日 作成</a>
        </div>

        <div class="bl_scheduleIndex_inner_content">
          <div class="c_calendar">
            <table>
              <thead>
                <tr>
                  <th><a href="{{ route('admin.web_busy_season.index', ['calendar_month' => $calendar->prev]) }}">&lt;</a>
                  </th>
                  <th><a href="{{ route('admin.web_busy_season.index', ['calendar_month' => $calendar->next]) }}">&gt;</a>
                  </th>
                  <th colspan="3">{{ $calendar->year_month }}</th>
                  <th colspan="2"><a href="{{ route('admin.web_busy_season.index') }}">Today</a></th>
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
        </div>

        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class='el_width1rem'>ID</th>
                <th class='el_width6rem'>繁忙日</th>
              </tr>

              @foreach ($busy_season_list as $busy_season)
                <tr>
                  <td class='el_center'>
                    {{ $busy_season->id }}
                  </td>
                  <td class='el_center'>
                    {{ $busy_season->busy_date ?? '' }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
      </div>
    </div>
  </div>

  {{-- 日程削除用フォーム --}}
  <form method="POST" id='js_form_busy_date'>
    @csrf
  </form>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>

  <script>
    /* 稼働可能日削除処理 */
    document.addEventListener('DOMContentLoaded', function() {
      let $form_busy_date = document.getElementById('js_form_busy_date'); // 削除用フォーム
      let $busy_date_list = document.querySelectorAll('.js_busy_date'); // 日程リスト

      // 稼働可能日をクリックしたら
      $busy_date_list.forEach(($busy_date) => {
        $busy_date.addEventListener('click', (e) => {
          e.preventDefault();
          let result = window.confirm(`${$busy_date.dataset.busy_date} を削除しますか？`);
          if (result) {
            $form_busy_date.action = $busy_date.dataset.action // form のactionを設定
            $form_busy_date.submit();
          }
        })
        console.log('稼働可能日削除処理');
      });
    });
  </script>
@endsection
