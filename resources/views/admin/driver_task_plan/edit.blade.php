@extends('layouts.admin.app')

@section('title')
  稼働依頼プラン 編集
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
          <h2>稼働依頼プラン 編集</h2>
        </div>
      </div>

      <div class="bl_edit_inner_content">
        <section class="bl_edit_inner_content_data">
          <form action="{{ route('admin.driver_task_plan.update', ['driver_task_plan_id' => $driver_task_plan->id]) }}" method="POST" class="js_confirm">

            @csrf
            <div class="bl_edit_inner_content_data_form">

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="task_id">ID</label>
                <input type="number" name='driver_task_id' id='driver_task_id'
                  value="{{ old('driver_task_id', $driver_task_plan->id ?? '') }}" readonly>
                <p class="el_error_msg">
                  @error('driver_task_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="name">名前</label>
                <input type="text" name='name' id='name' value="{{ old('name', $driver_task_plan->name ?? '') }}">
                <p class="el_error_msg">
                  @error('name')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              @if (in_array($driver_task_plan->id, [1]))
                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="system_price">システム利用料金</label>
                  <input type="text" name='system_price' id='system_price' value="{{ old('system_price', $driver_task_plan->system_price ?? '') }}">
                  <p class="el_error_msg">
                    @error('system_price')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              @endif

              @if (in_array($driver_task_plan->id, [2]))
                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="system_price">システム料金</label>
                  <div class="c_form_select">
                    <select name="system_price" id="system_price">
                      <option disabled selected value=''>
                        選択してください。
                      </option>
                      @for ($i = 3000; $i <= 9000; $i += 1000)
                        <option value="{{ $i }}"
                          {{ $i == old('system_price', $driver_task_plan->system_price) ? 'selected' : '' }}>
                          {{ $i }}
                        </option>
                      @endfor
                    </select>
                  </div>
                  <p class="el_error_msg">
                    @error('system_price')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              @endif

              @if (in_array($driver_task_plan->id, [3]))
                {{-- システム料金 --}}
                <input type="hidden" name='system_price' id='system_price' value="{{ old('system_price', $driver_task_plan->system_price ?? '') }}">
              @endif


              @if (in_array($driver_task_plan->id, [3]))
                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="system_price_percent">システム利用料金(運賃の%)</label>
                  <div class="c_form_select">
                    <select name="system_price_percent" id="system_price_percent">
                      <option disabled selected value=''>
                        選択してください。
                      </option>
                      @for ($i = 20; $i <= 40; $i += 10)
                        <option value="{{ $i }}"
                          {{ $i == old('system_price_percent', $driver_task_plan->system_price_percent) ? 'selected' : '' }}>
                          {{ $i }}
                        </option>
                      @endfor
                    </select>
                  </div>
                  <p class="el_error_msg">
                    @error('system_price_percent')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              @else
                <input type="hidden" name='system_price_percent' id='system_price_percent' value="{{ old('system_price_percent', $driver_task_plan->system_price_percent ?? '') }}">
              @endif



              {{-- <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="busy_system_price">システム料金(繁忙期)</label>
                <input type="text" name='busy_system_price' id='busy_system_price' value="{{ old('busy_system_price', $driver_task_plan->busy_system_price ?? '') }}">
                <p class="el_error_msg">
                  @error('busy_system_price')
                    {{ $message }}
                  @enderror
                </p>
              </div> --}}
              <input type="hidden" name='busy_system_price' id='busy_system_price' value="{{ old('busy_system_price', $driver_task_plan->busy_system_price ?? '') }}">

              {{-- <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="busy_system_price_percent">システム料金(繁忙期,運賃の%)</label>
                <input type="text" name='busy_system_price_percent' id='busy_system_price_percent' value="{{ old('busy_system_price_percent', $driver_task_plan->busy_system_price_percent ?? '') }}">
                <p class="el_error_msg">
                  @error('busy_system_price_percent')
                    {{ $message }}
                  @enderror
                </p>
              </div> --}}
              <input type="hidden" name='busy_system_price_percent' id='busy_system_price_percent' value="{{ old('busy_system_price_percent', $driver_task_plan->busy_system_price_percent ?? '') }}">



              {{-- <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="busy_system_price_percent_over">システム料金(繁忙期,運賃の%)</label>
                <input type="text" name='busy_system_price_percent_over' id='busy_system_price_percent_over' value="{{ old('busy_system_price_percent_over', $driver_task_plan->busy_system_price_percent_over ?? '') }}">
                <p class="el_error_msg">
                  @error('busy_system_price_percent_over')
                    {{ $message }}
                  @enderror
                </p>
              </div> --}}
              <input type="hidden" name='busy_system_price_percent_over' id='busy_system_price_percent_over' value="{{ old('busy_system_price_percent_over', $driver_task_plan->busy_system_price_percent_over ?? '') }}">


              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="emergency_price">緊急依頼料金</label>
                <input type="text" name='emergency_price' id='emergency_price' value="{{ old('emergency_price', $driver_task_plan->emergency_price ?? '') }}">
                <p class="el_error_msg">
                  @error('emergency_price')
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
