@extends('layouts.driver.app')

@section('title')
  アカウント編集
  @if ($type === 'icon')
    (アイコン)
  @elseif($type === 'email')
    (メールアドレス変更)
  @elseif($type === 'password')
    (パスワード変更)
  @elseif($type === 'office')
    (営業所登録)
  @elseif($type === 'user')
    (ユーザー情報)
  @endif
@endsection

@foreach ($errors->all() as $error)
  <script>
    console.log("バリデーション {{ $error }}");
  </script>
@endforeach

@section('content')
  @if (session('msg'))
  <div class="bl_msg">
    <p class="el_red">
      {{ session('msg') ?? '' }}
    </p>
  </div>
  @endif

  <div class="bl_driverEdit">
    <div class="bl_driverEdit_inner">
      <div class="bl_driverEdit_inner_head">
        <div class="bl_driverEdit_inner_head_ttl">
          <h2>ドライバーアカウント
            @if ($type === 'icon')
              (アイコン)
            @elseif($type === 'email')
              (メールアドレス変更)
            @elseif($type === 'password')
              (パスワード変更)
            @elseif($type === 'office')
              (営業所登録)
            @elseif($type === 'user')
              (ユーザー情報)
            @elseif($type === 'delete')
              (アカウント削除)
            @endif
            編集<span>/ driver edit</span>
          </h2>
        </div>
      </div>
      <div class="bl_driverEdit_inner_content">
        <div class="bl_driverEdit_inner_content_form">

          {{-- ユーザーアイコン画像 --}}
          @if ($type === 'icon')
            <img src="" alt="" id="js_file_icon_img" class="bl_driverEdit_inner_content_form_img">
            <form action="{{ route('driver.user.update', ['type' => $type]) }}" method="POST"
              enctype="multipart/form-data" class="js_confirm">
              @csrf
              <input type="file" name="icon_img" accept="image/*" id="icon_img" class="js_icon_input">
              @error('icon_img')
                <p class="el_error_msg">{{ $message }}</p>
              @enderror

              <div class="bl_driverEdit_inner_content_form_submit c_form_submit">
                <input type="submit" value="ユーザーアイコン更新">
              </div>
            </form>

            <script>
              /* アイコン画像が設置されたとき、プレビューする */
              let $file_icon_img = document.getElementById('js_file_icon_img'); // 画像
              let $icon_input = document.querySelector('.js_icon_input'); //input

              $icon_input.addEventListener('change', (e) => {
                const file = e.target.files;
                const reader = new FileReader();
                reader.readAsDataURL(file[0]);
                reader.onload = () => {
                  $file_icon_img.src = reader.result;
                };
              });

              // function setImage(e) {
              //   const file = e.target.files;
              //   const reader = new FileReader();
              //   reader.readAsDataURL(file[0]);
              //   reader.onload = () => {
              //     $file_icon_img.src = reader.result;
              //   };
              // }
            </script>

            {{-- メールアドレス --}}
          @elseif ($type === 'email')
            <form action="{{ route('driver.user.update', ['type' => $type]) }}" method="POST" class="js_confirm">
              @csrf

              <section class="bl_driverEdit_inner_content_form">
                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="email">メールアドレス</label>
                  <input type="text" name="email" value="{{ old('email', $driver->email) }}" id="email">
                  @error('email')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class="bl_driverEdit_inner_content_form_submit c_form_submit">
                  <input type="submit" value="メールアドレス変更">
                </div>
              </section>
            </form>

            {{-- パスワード --}}
          @elseif ($type === 'password')
            <form action="{{ route('driver.user.update', ['type' => $type]) }}" method="POST" class="js_confirm">
              <section class="bl_driverEdit_inner_content_form">
                @csrf

                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="current_password">現在のパスワード</label>
                  <input type="password" name="current_password" value="" id="current_password">
                  @error('current_password')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>
                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="password">新規パスワード</label>
                  <input type="password" name="password" value="" id="password">
                  @error('password')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>
                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="password_confirmation">確認用パスワード</label>
                  <input type="password" name="password_confirmation" value="" id="password_confirmation">
                </div>
                <div class="bl_driverEdit_inner_content_form_submit c_form_submit">
                  <input type="submit" value="パスワード変更">
                </div>
              </section>
            </form>

            {{-- 営業所登録 --}}
          @elseif($type === 'office')
            <form action="{{ route('driver.driver_register_delivery_office.upsert', ['type' => $type]) }}" method="POST" class="js_confirm">
              @csrf
              <section class="bl_driverEdit_inner_content_form">
                @foreach ($delivery_multi_list as $delivery_list)
                  <div class="bl_driverEdit_inner_content_form_checkboxWrap">
                    <div class="bl_driverEdit_inner_content_form_caption">
                      <h4>
                        {{ $delivery_list['company']['name'] }}
                      </h4>
                    </div>

                    <ul>
                      @foreach ($delivery_list['office_list'] as $office)
                        <li class="c_form_checkbox">
                          <input type="checkbox"
                            name='register_office[]'
                            value='{{ $office['id'] }}'
                            id='{{ $delivery_list['company']['name'] }}_{{ $office['name'] }}_{{ $office['id'] }}'
                            {{ in_array($office['id'], $register_office_id_list, true) ? 'checked' : '' }}>
                          <label
                            for="{{ $delivery_list['company']['name'] }}_{{ $office['name'] }}_{{ $office['id'] }}"
                            class='el_checkbox_label'>{{ $office['name'] }}
                          </label>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                @endforeach
                @error('register_office.*')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
                @error('register_office')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
                <div class="el_submit bl_driverEdit_inner_content_form_submit c_form_submit">
                  <input type="submit" value="営業所登録">
                </div>

                <div class="el_advice">
                  上記に存在しない営業所は
                  <a href="{{ route('driver.driver_register_delivery_office_memo.index') }}" class="c_normal_link">こちら</a>
                  から登録してください。
                </div>
              </section>
            </form>

            {{-- ユーザー情報 --}}
          @elseif ($type === 'user')
            <form action="{{ route('driver.user.update', ['type' => $type]) }}" method="POST" class="js_confirm">
              @csrf
              <section class="bl_driverEdit_inner_content_form">
                <div class="c_form_flex">
                  <div class="bl_driverEdit_inner_content_form_item c_form_item el_width_half">
                    <label for="name_sei">姓</label>
                    <input type="text" name="name_sei" value="{{ old('name_sei', $driver->name_sei) }}" id="name_sei">
                    @error('name_sei')
                      <p class="el_error_msg">{{ $message }}</p>
                    @enderror
                  </div>
                  <div class="bl_driverEdit_inner_content_form_item c_form_item el_width_half">
                    <label for="name_mei">名</label>
                    <input type="text" name="name_mei" value="{{ old('name_mei', $driver->name_mei) }}" id="name_mei">
                    @error('name_mei')
                      <p class="el_error_msg">{{ $message }}</p>
                    @enderror
                  </div>
                </div>

                <div class="c_form_flex">
                  <div class="bl_driverEdit_inner_content_form_item c_form_item el_width_half">
                    <label for="name_sei_kana">姓(カナ)</label>
                    <input type="text" name="name_sei_kana" value="{{ old('name_sei_kana', $driver->name_sei_kana) }}" id="name_sei_kana">
                    @error('name_sei_kana')
                      <p class="el_error_msg">{{ $message }}</p>
                    @enderror
                  </div>
                  <div class="bl_driverEdit_inner_content_form_item c_form_item el_width_half">
                    <label for="name_mei_kana">名(カナ)</label>
                    <input type="text" name="name_mei_kana" value="{{ old('name_mei_kana', $driver->name_mei_kana) }}" id="name_mei_kana">
                    @error('name_mei_kana')
                      <p class="el_error_msg">{{ $message }}</p>
                    @enderror
                  </div>
                </div>

                <div class="bl_driverEdit_inner_content_form_item c_form_item el_width_6rem">
                  <label for="gender_id">性別</label>
                  <div class="c_form_select">
                    <select name="gender_id" id="gender_id">
                      <option disabled selected>
                        選択してください。
                      </option>
                      @foreach ($gender_list as $gender)
                        <option
                          value="{{ $gender->id }}"
                          {{ $gender->id == old('gender_id', $driver->gender_id) ? 'selected' : '' }}>
                          {{ $gender->name ?? '' }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  @error('gender_id')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class="bl_driverEdit_inner_content_form_item c_form_item el_width_half">
                  <label for="">誕生日</label>
                  <input type="date" name="birthday" value="{{ old('birthday', $driver->birthday) }}" id="birthday">
                  @error('birthday')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class='bl_driverEdit_inner_content_form_item c_form_item'>
                  <label for="">郵便番号</label>
                  <input type="text" name="post_code1" value="{{ old('post_code1', $driver->post_code1) }}" class='el_width6rem'>
                  <span>-</span>
                  <input type="text" name="post_code2" value="{{ old('post_code2', $driver->post_code2) }}" class='el_width6rem'>
                  @error('post_code1')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                  @error('post_code2')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class="bl_driverEdit_inner_content_form_item c_form_item el_width_half">
                  <label for="addr1_id">都道府県</label>
                  <div class="c_form_select">
                    <select name="addr1_id" id="addr1_id">
                      <option disabled selected>
                        選択してください。
                      </option>
                      @foreach ($prefecture_list as $prefecture)
                        <option
                          value="{{ $prefecture->id }}"
                          {{ $prefecture->id == old('addr1_id', $driver->addr1_id) ? 'selected' : '' }}>
                          {{ $prefecture->name ?? '' }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  @error('addr1_id')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="addr2">市区町村</label>
                  <input type="text" name="addr2" value="{{ old('addr2', $driver->addr2) }}" id="addr2">
                  @error('addr2')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="addr3">丁目 番地 号</label>
                  <input type="text" name="addr3" value="{{ old('addr3', $driver->addr3) }}" id="addr3">
                  @error('addr3')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="addr4">建物名 部屋番号</label>
                  <input type="text" name="addr4" value="{{ old('addr4', $driver->addr4) }}" id="addr4">
                  @error('addr4')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="tel">電話番号</label>
                  <input type="number" name="tel" value="{{ old('tel', $driver->tel) }}" id="tel">
                  @error('tel')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="career">経歴</label>
                  <textarea name="career" id="career">{{ old('career', $driver->career) }}</textarea>
                  @error('career')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="introduction">紹介文</label>
                  <textarea name="introduction" id="introduction">{{ old('introduction', $driver->introduction) }}</textarea>
                  @error('introduction')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class="bl_driverEdit_inner_content_form_submit c_form_submit">
                  <input type="submit" value="登録情報更新">
                </div>

              </section>
            </form>
          @elseif ($type === 'delete')
            <form action="{{ route('driver.user.update', ['type' => $type]) }}" method="POST" class="js_confirm">
              <section class="bl_driverEdit_inner_content_form">
                @csrf

                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="password">現在のパスワード</label>
                  <input type="password" name="password" value="" id="password">
                  @error('password')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>
                <div class="bl_driverEdit_inner_content_form_item c_form_item">
                  <label for="password_confirmation">確認用パスワード</label>
                  <input type="password" name="password_confirmation" value="" id="password_confirmation">
                  @error('password_confirmation')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>
                <div class="bl_driverEdit_inner_content_form_submit c_form_submit">
                  <input type="submit" value="退会申請する">
                </div>
              </section>
            </form>
          @endif

        </div>
      </div>
    </div>
  </div>


  <script>
    /* テストコード */
    // document.addEventListener('DOMContentLoaded', function() {

    //   $email = document.getElementById('email');
    //   $gender_id = document.getElementById('gender_id');
    //   $birthday = document.getElementById('birthday');
    //   $post1 = document.getElementById('post_code1');
    //   $post2 = document.getElementById('post_code2');
    //   $addr1_id = document.getElementById('addr1_id');
    //   $addr2 = document.getElementById('addr2');
    //   $addr3 = document.getElementById('addr3');
    //   $tel = document.getElementById('tel');
    //   $career = document.getElementById('career');
    //   $introduction = document.getElementById('introduction');

    //   $email.value = 'hello@test.test';
    //   $gender_id.options[2].selected = true;
    //   $birthday.value = '2000-01-01';
    //   $post1.value = 123;
    //   $post2.value = 4567;
    //   $addr1_id.options[10].selected = true;
    //   $addr2.value = 'ぐんまー市';
    //   $addr3.value = '1丁目 1番地 マンション101';
    //   $tel.value = '1029384756';
    //   $career.value = 'キャリアcareer経歴けいれき';
    //   $introduction.value = '紹介introductionしょうかいショウカイ';
    // });
  </script>

@endsection
@section('script_bottom')
  <script src="{{ asset('./js/libs/Functions/form_parts.js') }}"></script>
@endsection
