@extends('layouts.admin.app')

@section('title')
  依頼者レビュー一覧
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
          <h2>依頼者レビュー一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        {{-- <div class="bl_index_inner_content_handle">
          <a href="{{ route('admin.delivery_office_task_review.create') }}" class="c_btn">作成</a>
        </div> --}}

        <section class="bl_index_inner_content_formBox">
          <button class="js_show_form_btn bl_index_inner_content_formBox_button">検索フォーム</button>
          <form action="{{ route('admin.delivery_office_task_review.index') }}" method="GET" class="js_form">

            <input type="hidden" name="driver_id" value="{{ $_GET['driver_id'] ?? '' }}">
            <input type="hidden" name="delivery_office_id" value="{{ $_GET['delivery_office_id'] ?? '' }}">

            {{-- <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="keyword">検索ワード</label>
              <input type="text" name='keyword' id="keyword" value="{{ isset($_GET['keyword']) ? $_GET['keyword'] : '' }}">
            </div>

            <div class="c_form_submit bl_index_inner_content_formBox_item">
              <input type="submit" value='検索'>
            </div> --}}


            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_score">評価点</label>
              <input type="number" name='from_score' id="from_score"
                value="{{ $_GET['from_score'] ?? '' }}" class='el_width12rem'>
              <span>-</span>
              <input type="number" name='to_score'
                value="{{ $_GET['to_score'] ?? '' }}" class='el_width12rem'>
            </div>


            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_task_date">稼働日</label>
              <input type="date" name='from_task_date' id="from_task_date"
                value="{{ $_GET['from_task_date'] ?? '' }}" class='el_width12rem'>
              <span>-</span>
              <input type="date" name='to_task_date' id="to_task_date"
                value="{{ $_GET['to_task_date'] ?? '' }}" class='el_width12rem'>
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
                <th class="el_width4rem">ID</th>
                <th class="el_width3rem">評価点</th>
                <th class="el_width6rem">稼働日</th>
                <th class="el_width11rem">依頼者(レビュー対象)</th>
                <th class="el_width11rem">ドライバー(レビュー者)</th>
                <th class="el_width7rem">公開ステータス</th>
                <th class="el_width4rem">編集</th>
              </tr>

              @foreach ($review_list as $review)
                <tr>
                  <td class='el_center'><a href="{{ route('admin.delivery_office_task_review.show', ['review_id' => $review->id]) }}"
                      class='c_link el_btn'>{{ $review->id }}</a></td>
                  <td class='el_center'>{{ $review->score }}</td>
                  <td>
                    <a href="{{ route('admin.driver_task.show', ['task_id' => $review->driver_task_id]) }}" class="c_normal_link">
                      {{ $review->joinTask->taskDateYmd ?? '' }}
                    </a>
                  </td>

                  <td>
                    @if ($review->delivery_office_id && $review->joinOffice)
                      <a href="{{ route('admin.delivery_office.show', ['office_id' => $review->delivery_office_id]) }}" class="c_normal_link">
                        {{ $review->joinOffice->name ?? '' }}
                      </a>
                    @elseif ($review->delivery_office_id && !$review->joinOffice)
                      <a href="{{ route('admin.delivery_office.show', ['office_id' => $review->delivery_office_id]) }}" class="c_normal_link">
                        データなしorソフト削除済み
                      </a>
                    @else
                      なし
                    @endif
                  </td>

                  <td>
                    @if ($review->driver_id && $review->joinDriver)
                      <a href="{{ route('admin.driver.show', ['driver_id' => $review->driver_id]) }}" class="c_normal_link">
                        {{ $review->joinDriver->full_name ?? '' }}
                      </a>
                    @elseif ($review->driver_id && !$review->joinDriver)
                      <a href="{{ route('admin.driver.show', ['driver_id' => $review->driver_id]) }}" class="c_normal_link">
                        データなしorソフト削除済み
                      </a>
                    @else
                      なし
                    @endif
                  </td>

                  <td class='el_center'>{{ $review->joinPublicStatus->name ?? '' }}</td>

                  <td class='el_center'><a href="{{ route('admin.delivery_office_task_review.edit', ['review_id' => $review->id]) }}"
                      class='c_link el_btn'>編集</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $review_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
