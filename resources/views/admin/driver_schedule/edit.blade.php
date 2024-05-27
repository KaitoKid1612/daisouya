@extends('layouts.admin.app')

@section('title')
  稼働可能日スケジュール 編集
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  @if ($schedule)
    <div class="bl_edit">
      <div class="bl_edit_inner">
        <div class="bl_edit_inner_head">
          <div class="bl_edit_inner_head_ttl">
            <h2>稼働可能日スケジュール 編集</h2>
          </div>
        </div>

        <div class="bl_edit_inner_content">
          <section class="bl_edit_inner_content_data">
            <form action="{{ route('admin.driver_schedule.update', ['schedule_id' => $schedule->id]) }}" method="POST" class="js_confirm">
              @csrf
              <div class="bl_edit_inner_content_data_form">

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="available_date">稼働可能日</label>
                  <input type="date" name='available_date' id='available_date'
                    value="{{ old('available_date', $schedule->available_date ?? '') }}">
                  <p class="el_error_msg">
                    @error('available_date')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="driver_id">ドライバーID</label>
                  <input type="number" name='driver_id' id='driver_id' value="{{ old('available_date', $schedule->driver_id ?? '') }}">
                  <p class="el_error_msg">
                    @error('driver_id')
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
  @else
    <section class="bl_noData">
      <div class="bl_noData_inner">
        <p>
          このページは存在しません。
        </p>
      </div>
    </section>
  @endif
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
