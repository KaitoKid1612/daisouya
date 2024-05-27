@extends('layouts.admin.app')

@section('title')
  ドライバーレビュー 編集
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  <div class="bl_edit">
    <div class="bl_edit_inner">
      <div class="bl_edit_inner_head">
        <div class="bl_edit_inner_head_ttl">
          <h2>ドライバーレビュー 編集</h2>
        </div>
      </div>

      <div class="bl_edit_inner_content">
        <section class="bl_edit_inner_content_data">
          <form action="{{ route('admin.driver_task_review.update', ['review_id' => $review->id]) }}" method="POST" class="js_confirm">

            @csrf
            <div class="bl_edit_inner_content_data_form">

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="task_id">稼働ID</label>
                <input type="number" name='driver_task_id' id='driver_task_id'
                  value="{{ old('driver_task_id', $review->driver_task_id ?? '') }}" readonly>
                <p class="el_error_msg">
                  @error('driver_task_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="driver_task_review_public_status">ステータス</label>
                <div class="c_form_select">
                  <select name="driver_task_review_public_status" id="driver_task_review_public_status">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($driver_task_review_public_status_list as $status)
                      <option value="{{ $status->id }}"
                        {{ $status->id == old('driver_task_review_public_status', $review->driver_task_review_public_status_id) ? 'selected' : '' }}>
                        {{ $status->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <p class="el_error_msg">
                  @error('driver_task_review_public_status')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="score">評価 1~5</label>
                <input type="number" name='score' id='score' value="{{ old('score', $review->score ?? '') }}">
                <p class="el_error_msg">
                  @error('score')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="title">タイトル</label>
                <input type="text" name='title' id='title' value="{{ old('title', $review->title ?? '') }}">
                <p class="el_error_msg">
                  @error('title')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="text">レビュー内容</label>
                <textarea name='text' id='text'>{{ old('text', $review->text ?? '') }}</textarea>
                <p class="el_error_msg">
                  @error('text')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item el_submit">
                <input type="submit" value="編集" class='c_btn'>
              </div>

            </div>
          </form>
        </section>
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
      //   $delivery_office_id.value = '1';
      //   $driver_task_id.value = '1';
      //   $driver_task_review_public_status.options[1].selected = true;
      // });
    </script>
  @endif

@endsection
