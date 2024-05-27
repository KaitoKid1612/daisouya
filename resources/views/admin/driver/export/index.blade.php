@extends('layouts.admin.app')

@section('title')
  ドライバーエクスポート
@endsection

@section('content')
  <div class="bl_export">
    <div class="bl_export_inner">
      <div class="bl_export_inner_head">
        <div class="bl_export_inner_head_ttl">
          <h2>ドライバーエクスポート<span>/ driver export</span></h2>
        </div>
      </div>
      <div class="bl_export_inner_content">
        <form method="POST" action="{{ route('admin.driver.export.read') }}">
          @csrf
          <div class="bl_export_inner_content_form">

            <div class="bl_export_inner_content_form_item el_width12rem ">
              <label for="orderby">並び順</label>
              <div class="c_form_select">

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
            </div>


            <div class="c_form_item el_width12rem bl_export_inner_content_form_item">
              <label for="addr1_id">都道府県</label>
              <div class="c_form_select">
                <select name="addr1_id" id="addr1_id">
                  <option disabled selected>
                    選択。
                  </option>
                  <option value=''>
                    指定なし
                  </option>
                  @foreach ($prefecture_list as $prefecture)
                    <option value="{{ $prefecture->id }}">
                      {{ $prefecture->name ?? '' }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="c_form_item el_width12rem bl_export_inner_content_form_item">
              <label for="gender_id">性別</label>
              <div class="c_form_select">
                <select name="gender_id" id="gender_id">
                  <option disabled selected>
                    選択。
                  </option>
                  <option value=''>
                    指定なし
                  </option>
                  @foreach ($gender_list as $gender)
                    <option value="{{ $gender->id }}">
                      {{ $gender->name ?? '' }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="c_form_item bl_export_inner_content_form_item">
              <label for="from_age">年齢</label>
              <input type="number" name="from_age" id="from_age" min="0" value="{{ old('from_age', $_GET['from_age'] ?? '') }}" class='el_width12rem'>

              <span>-</span>
              <input type="number" name="to_age" id="to_age" min="0" value="{{ old('to_age', $_GET['to_age'] ?? '') }} "class='el_width12rem'>
            </div>


            <div class="c_form_item bl_export_inner_content_form_item">
              <label for="from_review_avg_score">評価 1~5.0の範囲</label>
              <input type="number" name="from_review_avg_score" id="from_review_avg_score" min="0"
                step="0.1"
                max="5" value="{{ $_GET['from_review_avg_score'] ?? '' }}" class='el_width12rem'>

              <span>-</span>
              <input type="number" name="to_review_avg_score" id="to_review_avg_score" min="0"
                max="5"
                step="0.1" value="{{ $_GET['to_review_avg_score'] ?? '' }}" class='el_width12rem'>
            </div>

            <div class="c_form_item bl_export_inner_content_form_item">
              <label for="from_task_count">稼働数</label>
              <input type="number" name="from_task_count" id="from_task_count" min="0"
                value="{{ $_GET['from_task_count'] ?? '' }} " class='el_width12rem'>
              <span>-</span>
              <input type="number" name="to_task_count" id="to_task_count" min="0" class='el_width12rem'>
            </div>


            <div class="c_form_item bl_export_inner_content_form_item">
              <label for="from_created_at">作成日</label>
              <input type="date" name='from_created_at' id="from_created_at"
                value="{{ $_GET['from_created_at'] ?? '' }}" class='el_width12rem'>
              <span>-</span>
              <input type="date" name='to_created_at'
                value="{{ $_GET['to_created_at'] ?? '' }}" class='el_width12rem'>
            </div>


            <div class="c_form_item bl_export_inner_content_form_item">
              <label for="from_updated_at">更新日</label>
              <input type="date" name='from_updated_at' id="from_updated_at"
                value="{{ $_GET['from_updated_at'] ?? '' }}" class='el_width12rem'>
              <span>-</span>

              <input type="date" name='to_updated_at'
                value="{{ $_GET['to_updated_at'] ?? '' }}" class='el_width12rem'>
            </div>

            <div class="c_form_item bl_export_inner_content_form_item">
              <div class="c_form_checkbox">
                <input type="checkbox" name="is_soft_delete" id="is_soft_delete" value="1">
                <label for="is_soft_delete">ソフト削除済みも含める</label>
              </div>
            </div>


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
@endsection
