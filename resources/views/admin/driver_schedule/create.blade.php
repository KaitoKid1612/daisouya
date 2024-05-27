@extends('layouts.admin.app')

@section('title')
  稼働可能日スケジュール 作成
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
          <h2>稼働可能日スケジュール 作成</h2>
        </div>
      </div>

      <div class="bl_create_inner_content">
        <section class="bl_create_inner_content_data">
          <form action="{{ route('admin.driver_schedule.store') }}" method="POST" class="js_confirm">
            @csrf

            <div class="bl_create_inner_content_data_form">

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="available_date">稼働可能日</label>
                <input type="date" name='available_date' id='available_date'
                  value='{{ old('available_date2') }}'>
                <p class="el_error_msg">
                  @error('available_date')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="driver_id">ドライバーID <span class='color_main'>{{ $driver->full_name ?? '' }}</span></label>
                <input type="number" name='driver_id' id='driver_id' value='{{ old('driver_id', $_GET['driver_id'] ?? '') }}' {{ isset($_GET['driver_id']) ? 'readonly' : '' }}>
                <p class="el_error_msg">
                  @error('driver_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_create_inner_content_data_form_item el_submit">
                <input type="submit" value="作成" class='c_btn'>
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
      //   $available_date = document.getElementById('available_date');
      //   $driver_id = document.getElementById('driver_id');

      //   $available_date.value = '2022-12-01';
      //   $driver_id.value = 1;
      // });
    </script>
  @endif
@endsection
