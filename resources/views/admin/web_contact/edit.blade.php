@extends('layouts.admin.app')

@section('title')
  お問い合わせ 編集
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  @if ($web_contact)
    <div class="bl_edit">
      <div class="bl_edit_inner">
        <div class="bl_edit_inner_head">
          <div class="bl_edit_inner_head_ttl">
            <h2>お問い合わせ 編集</h2>
          </div>
        </div>

        <div class="bl_edit_inner_content">
          <section class="bl_edit_inner_content_data">
            <form action="{{ route('admin.web_contact.update', ['contact_id' => $web_contact->id]) }}" method="POST" enctype="multipart/form-data" class="js_confirm">
              @csrf
              <div class="bl_edit_inner_content_data_form">

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="web_contact_status_id">ステータス</label>
                  <div class="c_form_select">
                    <select name="web_contact_status_id" id="web_contact_status_id">
                      <option disabled selected>
                        選択してください。
                      </option>
                      @foreach ($web_contact_status_list as $web_contact_status)
                        <option value="{{ $web_contact_status->id }}"
                          {{ $web_contact_status->id == old('web_contact_status_id', $web_contact->web_contact_status_id) ? 'selected' : '' }}>
                          {{ $web_contact_status->name ?? '' }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <p class="el_error_msg">
                    @error('web_contact_status_id')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item el_submit">
                  <input type="submit" value="編集" class='c_btn'>
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
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
