@extends('layouts.driver.app')

@section('title')
  レビュー作成
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  <div class="bl_reviewCreate">
    <div class="bl_reviewCreate_inner">
      <div class="bl_reviewCreate_inner_head">
        <div class="bl_reviewCreate_inner_head_ttl">
          <h2>今回のレビュー<span>/ review</span></h2>
        </div>
      </div>
      <div class="bl_reviewCreate_inner_content">
        @if ($task && !$review)
          <div class="bl_reviewCreate_inner_content_form">
            <form method="POST" action="{{ route('driver.delivery_office_task_review.store') }}" class="js_confirm">
              @csrf

              <input type="hidden" name="driver_task_id" value="{{ $task->id }}">

              <section class="bl_reviewCreate_inner_content_form_request">
                <div class="bl_reviewCreate_inner_content_form_request_item">
                  <label for="driver">依頼者</label>
                  <p>{{ $task->joinOffice->joinCompany->name ?? $task->joinOffice->delivery_company_name ?? '' }} {{ $task->joinOffice->name ?? '' }} {{ $task->joinOffice->full_name ?? '' }}</p>
                </div>
                <div class="bl_reviewCreate_inner_content_form_request_item">
                  <label for="driver">集荷先</label>
                  <p>{{ $task->full_post_code ?? '' }} {{ $task->full_addr ?? '' }}</p>
                </div>

                <div class="bl_reviewCreate_inner_content_form_request_item">
                  <label for="task_date">稼働日</label>
                  <p>{{ $task->taskDateYmd ?? '' }}</p>
                </div>

                <div class="c_form_item bl_reviewCreate_inner_content_form_request_item">
                  <label for="score">評価</label>
                  <div class="bl_reviewCreate_inner_content_form_request_item_selectbox el_width12rem">
                    <select name="score" id="score">
                      <option disabled selected>
                        ★ の選択
                      </option>
                      @for ($i = 1; $i < 6; $i++)
                        <option value={{ $i }}
                          {{ old('score') == $i ? 'selected' : '' }}>
                          {{ str_repeat('★', $i) }}
                        </option>
                      @endfor
                    </select>
                  </div>
                  <div class="bl_reviewCreate_inner_content_form_request_item_error">
                    <p class='bl_reviewCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('score')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="c_form_item bl_reviewCreate_inner_content_form_request_item">
                  <label for="title">タイトル</label>
                  <input type="text" name="title" id="title" value="{{ old('title') }}">
                  <div class="bl_reviewCreate_inner_content_form_request_item_error">
                    <p class='bl_reviewCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('title')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="c_form_item bl_reviewCreate_inner_content_form_request_item">
                  <label for="text">内容</label>
                  <textarea name='text' id='text'
                    placeholder="依頼者の対応に関して&#10;お気付きの点がございましたらご記入下さい">{{ old('text') }}</textarea>
                  <div class="bl_reviewCreate_inner_content_form_request_item_error">
                    <p class='bl_reviewCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('text')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>
              </section>


              <div class="bl_reviewCreate_inner_content_form_submit">
                <input type="submit" value="登録">
              </div>

            </form>
          </div>
        @else
          <p>レビューできません。</p>
        @endif
      </div>
    </div>
  </div>

  @if (config('app.env') === 'local')
    <script>
      /**
       *  テスト用フォーム自動入力
       * */
      // document.addEventListener('DOMContentLoaded', function() {
      //   $score = document.getElementById('score');
      //   $title = document.getElementById('title');
      //   $text = document.getElementById('text');
      //   $driver_id = document.getElementById('driver_id');
      //   $delivery_office_id = document.getElementById('delivery_office_id');
      //   $driver_task_id = document.getElementById('driver_task_id');
      //   $driver_task_review_public_status = document.getElementById('driver_task_review_public_status');

      //   $score.value = 3;
      //   $title.value = 'ハロー';
      //   $text.value = 'はろーーーーー';
      //   $driver_id.value = '1';
      //   $driver_task_id.value = 1;
      //   $driver_task_review_public_status.options[1].selected = true;
      // });
    </script>
  @endif
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
