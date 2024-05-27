@extends('layouts.admin.app')

@section('title')
  ドライバー 編集
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  @if ($register_request)
    <div class="bl_edit">
      <div class="bl_edit_inner">
        <div class="bl_edit_inner_head">
          <div class="bl_edit_inner_head_ttl">
            <h2>ドライバー登録申請 編集</h2>
          </div>
        </div>

        <div class="bl_edit_inner_content">
          <section class="bl_edit_inner_content_data">
            <form action="{{ route('admin.register_request_driver.update', ['register_request_id' => $register_request->id]) }}" method="POST" enctype="multipart/form-data" class="js_confirm">
              @csrf
              <div class="bl_edit_inner_content_data_form">

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="register_request_status_id">登録申請ステータス</label>
                  <div class="c_form_select">
                    <select name="register_request_status_id" id="register_request_status_id">
                      <option disabled selected>
                        選択してください。
                      </option>
                      @if ($register_request_status_list)
                        @foreach ($register_request_status_list as $register_request_status)
                          <option value="{{ $register_request_status->id }}"
                            {{ $register_request_status->id == $register_request->register_request_status_id ? 'selected' : '' }}>
                            {{ $register_request_status->name ?? '' }}
                          </option>
                        @endforeach
                      @endif
                    </select>
                  </div>

                  <p class="el_error_msg">
                    @error('register_request_status_id')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                {{-- ドライバーが存在していない or ドライバーは存在するがドライバープランが指定されていない --}}
                @if (!$driver || ($driver && !$driver->driver_plan_id))
                  <div class="c_form_item bl_edit_inner_content_data_form_item">
                    <label for="driver_plan_id">ドライバープラン</label>
                    <div class="c_form_select">
                      <select name="driver_plan_id" id="driver_plan_id">
                        <option disabled selected>
                          選択してください。
                        </option>
                        @if ($driver_plan_list)
                          @foreach ($driver_plan_list as $driver_plan)
                            <option value="{{ $driver_plan->id }}"
                              {{ $driver_plan->id == old('driver_plan_id', $register_request->driver_plan_id) ? 'selected' : '' }}>
                              {{ $driver_plan->name ?? '' }}
                            </option>
                          @endforeach
                        @endif
                      </select>
                    </div>

                    <p class="el_error_msg">
                      @error('driver_plan_id')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                @endif

                <div class="c_form_item bl_edit_inner_content_data_form_item el_submit">
                  <input type="submit" value="編集" class='c_btn'>
                </div>

                <div class="bl_edit_inner_content_data_form_advice">
                  <p>※こちらの操作を行うと該当ユーザーへメールが送られます。</p>
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

        //   $email = document.getElementById('email');
        //   $password = document.getElementById('password');
        //   $password_confirmation = document.getElementById('password_confirmation');
        //   $gender_id = document.getElementById('gender_id');
        //   $birthday = document.getElementById('birthday');
        //   $addr1_id = document.getElementById('addr1_id');
        //   $addr2 = document.getElementById('addr2');
        //   $addr3 = document.getElementById('addr3');
        //   $tel = document.getElementById('tel');
        //   $career = document.getElementById('career');
        //   $introduction = document.getElementById('introduction');


        //   $email.value = 'hello@test.test';
        //   $password.value = 'test1234';
        //   $password_confirmation.value = 'test1234';
        //   $gender_id.options[2].selected = true;
        //   $birthday.value = '2000-01-01';
        //   $addr1_id.options[10].selected = true;
        //   $addr2.value = 'ぐんまー市';
        //   $addr3.value = '1丁目 1番地 マンション101';
        //   $tel.value = '1029384756';
        //   $career.value = 'キャリアcareer経歴けいれき';
        //   $introduction.value = '紹介introductionしょうかいショウカイ';
        // });
      </script>
    @endif
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
  <script src="{{ asset('js/libs/Functions/driver_task.js') }}"></script>
@endsection
