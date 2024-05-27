@extends('layouts.admin.app')

@section('title')
  ドライバー一覧
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
          <h2>ドライバー一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        <div class="bl_index_inner_content_handle">
          <a href="{{ route('admin.driver.create') }}" class="c_btn bl_index_inner_content_handle_item">作成</a>
          <a href="{{ route('admin.driver.export.index') }}" class="c_btn bl_index_inner_content_handle_item">エクスポート</a>
        </div>


        <section class="bl_index_inner_content_formBox">
          <button class="js_show_form_btn bl_index_inner_content_formBox_button">検索フォーム</button>
          <form action="{{ route('admin.driver.index') }}" method="GET" class="js_form">

            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="keyword">検索ワード</label>
              <input type="text" name='keyword' id="keyword" value="{{ $_GET['keyword'] ?? '' }}" class="el_max_width45rem">
            </div>

            <div class="c_form_submit bl_index_inner_content_formBox_item">
              <input type="submit" value='検索'>
            </div>


            <div class="c_form_item el_width12rem bl_index_inner_content_formBox_item">
              <label for="addr1_id">都道府県</label>
              <div class="c_form_select">
                <select name="addr1_id" id="addr1_id">
                  <option disabled selected>
                    選択してください。
                  </option>
                  <option value=''>
                    指定なし
                  </option>
                  @foreach ($prefecture_list as $prefecture)
                    <option value="{{ $prefecture->id }}" {{ isset($_GET['addr1_id']) && $_GET['addr1_id'] == $prefecture->id ? 'selected' : '' }}>

                      {{ $prefecture->name ?? '' }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_review_avg_score">評価 1~5.0の範囲</label>
              <input type="number" name="from_review_avg_score" id="from_review_avg_score" min="0"
                step="0.1"
                max="5" value="{{ $_GET['from_review_avg_score'] ?? '' }}" class='el_width12rem'>

              <span>-</span>
              <input type="number" name="to_review_avg_score" id="to_review_avg_score" min="0"
                max="5"
                step="0.1" value="{{ $_GET['to_review_avg_score'] ?? '' }}"
                class='el_width12rem'>
            </div>

            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_task_count">稼働数</label>
              <input type="number" name="from_task_count" id="from_task_count" min="0"
                value="{{ $_GET['from_task_count'] ?? '' }} " class='el_width12rem'>
              <span>-</span>
              <input type="number" name="to_task_count" id="to_task_count" min="0"
                value="{{ $_GET['to_task_count'] ?? '' }}" class='el_width12rem'>
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
                <th class='el_width3rem'>申請状況</th>
                <th class='el_width5rem'>ドライバープラン</th>
                <th class='el_width3rem'>削除状態</th>
                <th class='el_width8rem'>名前</th>
                <th class='el_width12rem'>メールアドレス</th>
                <th class='el_width4rem'>評価点</th>
                <th class='el_width4rem'>稼働数</th>
                <th class='el_width8rem'>作成日</th>
                <th class='el_width4rem'>編集</th>
              </tr>

              @foreach ($driver_list as $driver)
                <tr>
                  <td class='el_center'>
                    <a href="{{ route('admin.driver.show', ['driver_id' => $driver->id]) }}"
                      class="c_btn el_btn">{{ $driver->id }}</a>
                  </td>
                  <td>{{ $driver->joinDriverEntryStatusId->name ?? 'データなし' }}</td>
                  <td>{{ $driver->joinDriverPlan->name ?? 'データなし' }}</td>
                  <td>{{ $driver->trashed() ? 'ソフト削除' : '' }}</td>
                  <td>{{ $driver->full_name }}</td>
                  <td>{{ $driver->email }}</td>
                  <td>{{ $driver->join_driver_review_avg_score }}</td>
                  <td>{{ $driver->join_task_count }}</td>
                  <td>{{ $driver->created_at }}</td>
                  <td class='el_center'><a
                      href="{{ route('admin.driver.edit', ['driver_id' => $driver->id]) }}"
                      class='c_btn el_btn'>編集</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $driver_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
