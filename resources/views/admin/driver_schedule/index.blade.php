@extends('layouts.admin.app')

@section('title')
  ドライバー稼働可能スケジュール一覧
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
          <h2>ドライバー稼働可能スケジュール一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        {{-- <div class="bl_index_inner_content_handle">
          <a href="{{ route('admin.driver_schedule.create') }}" class="c_btn">作成</a>
        </div> --}}


        <section class="bl_index_inner_content_formBox">
          <button class="js_show_form_btn bl_index_inner_content_formBox_button">検索フォーム</button>
          <form action="{{ route('admin.driver_schedule.index') }}" method="GET" class="js_form">
            <input type="hidden" name="driver_id" value="{{ $_GET['driver_id'] ?? '' }}">

            {{-- <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="keyword">検索ワード</label>
              <input type="text" name='keyword' id="keyword" value="{{ isset($_GET['keyword']) ? $_GET['keyword'] : '' }}">
            </div>

            <div class="c_form_submit bl_index_inner_content_formBox_item">
              <input type="submit" value='検索'>
            </div> --}}


            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="available_date">稼働可能日</label>
              <input type="date"
                name='from_available_date' id="available_date" value="{{ $_GET['from_available_date'] ?? '' }}"
                class='el_width12rem'>
              <span>-</span>
              <input type="date" name='to_available_date'
                value="{{ $_GET['to_available_date'] ?? '' }} "class='el_width12rem'>
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
                <th class='el_width3rem'>ID</th>
                <th class='el_width7rem'>稼働可能日</th>
                <th class='el_width11rem'>ドライバー名</th>
                <th class='el_width11rem'>作成日</th>
                <th class='el_width11rem'>更新日</th>
                <th class='el_width4rem'>編集</th>
                <th class='el_width4rem'>削除</th>
              </tr>

              @foreach ($schedule_list as $schedule)
                <tr>
                  <td class='el_center'>{{ $schedule->id }}</td>
                  <td>{{ $schedule->available_date }}</td>
                  <td>
                    <a
                      href="{{ $schedule->joinDriver ? route('admin.driver.show', ['driver_id' => $schedule->driver_id]) : 'javascript:void(0)' }}">{{ $schedule->joinDriver->full_name ?? 'なし' }}</a>
                  </td>
                  <td>{{ $schedule->updated_at }}</td>
                  <td>{{ $schedule->created_at }}</td>
                  <td class='el_center'>
                    <a
                      href="{{ route('admin.driver_schedule.edit', ['schedule_id' => $schedule->id]) }}"
                      class='c_btn el_btn'>
                      編集
                    </a>
                  </td>
                  <td class='el_center'>
                    <form action="{{ route('admin.driver_schedule.destroy', ['schedule_id' => $schedule->id]) }}" method="POST" class="js_confirm">
                      @csrf
                      <input type="submit" value="削除" class="c_btn el_btn el_bg_red">
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $schedule_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
