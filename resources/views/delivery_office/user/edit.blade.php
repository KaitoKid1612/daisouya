@extends('layouts.delivery_office.app')

@section('title')
  アカウント編集
  @if ($type === 'email')
    (メールアドレス変更)
  @elseif($type === 'password')
    (パスワード変更)
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

  <div class="bl_officeAccountEdit">
    <div class="bl_officeAccountEdit_inner">
      <div class="bl_officeAccountEdit_inner_head">
        <div class="bl_officeAccountEdit_inner_head_ttl">
          <h2>アカウント
            @if ($type === 'email')
              (メールアドレス変更)
            @elseif($type === 'password')
              (パスワード変更)
            @elseif($type === 'delete')
              (アカウント削除)
            @endif
            <span>/ account</span>
          </h2>
        </div>
      </div>

      <div class="bl_officeAccountEdit_inner_content">
        <div class="bl_officeAccountEdit_inner_content_form">

          {{-- メールアドレス変更時のフォーム --}}
          @if ($type === 'email')
            <form action="{{ route('delivery_office.user.update', ['type' => 'email']) }}" method="POST" class="js_confirm">
              @csrf

              <section class="bl_officeAccountEdit_inner_content_form">

                <div class="bl_officeAccountEdit_inner_content_form_item c_form_item">
                  <label for="email">メールアドレス</label>
                  <input type="text" name="email" value="{{ old('email', $office->email) }}" id="email">
                  @error('email')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class="bl_officeAccountEdit_inner_content_form_submit c_form_submit">
                  <input type="submit" value="メールアドレス変更">
                </div>
              </section>
            </form>
            {{-- パスワード変更時のフォーム --}}
          @elseif ($type === 'password')
            <form action="{{ route('delivery_office.user.update', ['type' => 'password']) }}" method="POST" class="js_confirm">
              @csrf

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item'>
                <label for="">現在のパスワード</label>
                <input type="password" name="current_password">
                @error('current_password')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>
              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item'>
                <label for="">パスワード</label>
                <input type="password" name="password">
                @error('password')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item'>
                <label for="">確認 パスワード</label>
                <input type="password" name="password_confirmation"></label>
                @error('password_confirmation')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>


              <div class='bl_officeAccountEdit_inner_content_form_submit c_form_submit'>
                <input type="submit" value="パスワード変更">
              </div>

            </form>

            {{-- 依頼者アカウント情報変更(パスワード以外) --}}
          @elseif ($type === 'user')
            <form action="{{ route('delivery_office.user.update', ['type' => 'user']) }}" method="POST" class="js_confirm">
              @csrf

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item el_width_half'>
                <label for="delivery_company_id">配送会社</label>
                <div class="c_form_select">
                  <select name="delivery_company_id" id="delivery_company_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    <option value="None" {{ null == $office->delivery_company_id ? 'selected' : '' }}>
                      所属なし
                    </option>
                    @foreach ($company_list as $company)
                      <option value="{{ $company->id }}"
                        {{ $company->id == old('delivery_company_id', $office->delivery_company_id) ? 'selected' : '' }}>
                        {{ $company->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>

                @error('delivery_company_id')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class="c_form_item bl_officeAccountEdit_inner_content_form_item js_form_delivery_company_name">
                <label for="delivery_company_name">会社名</label>
                <input type="text" name='delivery_company_name' id='delivery_company_name' value="{{ old('delivery_company_name', $office->delivery_company_name ?? '') }}">
                <p class="el_error_msg">
                  @error('delivery_company_name')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item'>
                <label for="">営業所名・デポ名</label>
                <input type="text" name="name" value="{{ old('name', $office->name) }}">
                @error('name')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_caption'>
                <h3>営業所 集荷先住所</h3>
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item'>
                <label for="">郵便番号</label>
                <input type="text" name="post_code1" value="{{ old('post_code1', $office->post_code1) }}" class='el_width6rem'>
                <span>-</span>
                <input type="text" name="post_code2" value="{{ old('post_code2', $office->post_code2) }}" class='el_width6rem'>
                @error('post_code1')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
                @error('post_code2')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item el_width_half'>
                <label for="">都道府県</label>
                <div class="c_form_select">
                  <select name="addr1_id" id="addr1_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($prefecture_list as $prefecture)
                      <option
                        value="{{ $prefecture->id }}"
                        {{ $prefecture->id == old('', $office->addr1_id) ? 'selected' : '' }}>
                        {{ $prefecture->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>

                @error('addr1_id')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item'>
                <label for="">市区町村</label>
                <input type="text" name="addr2" value="{{ old('addr2', $office->addr2) }}">
                @error('addr2')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item'>
                <label for="">丁目 番地 号</label>
                <input type="text" name="addr3" value="{{ old('addr3', $office->addr3) }}">
                @error('addr3')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item'>
                <label for="">建物名 部屋番号</label>
                <input type="text" name="addr4" value="{{ old('addr4', $office->addr4) }}">
                @error('addr4')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_caption'>
                <h3>担当者 情報</h3>
              </div>

              <div class="c_form_flex">
                <div class='bl_officeAccountEdit_inner_content_form_item c_form_item el_width_half'>
                  <label for="">担当者名 姓</label>
                  <input type="text" name="manager_name_sei" value="{{ old('manager_name_sei', $office->manager_name_sei) }}">
                  @error('manager_name_sei')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class='bl_officeAccountEdit_inner_content_form_item c_form_item el_width_half'>
                  <label for="">担当者名 名</label>
                  <input type="text" name="manager_name_mei" value="{{ old('manager_name_mei', $office->manager_name_mei) }}">
                  @error('manager_name_mei')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>
              </div>

              <div class="c_form_flex">
                <div class='bl_officeAccountEdit_inner_content_form_item c_form_item el_width_half'>
                  <label for="">担当者名 姓 (カナ)</label>
                  <input type="text" name="manager_name_sei_kana" value="{{ old('manager_name_sei_kana', $office->manager_name_sei_kana) }}">
                  @error('manager_name_sei_kana')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class='bl_officeAccountEdit_inner_content_form_item c_form_item el_width_half'>
                  <label for="">担当者名 姓 (カナ)</label>
                  <input type="text" name="manager_name_mei_kana" value="{{ old('manager_name_mei_kana', $office->manager_name_mei_kana) }}">
                  @error('manager_name_mei_kana')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item el_width_half'>
                <label for="">担当者 電話番号</label>
                <input type="number" name="manager_tel" value="{{ old('manager_tel', $office->manager_tel) }}">
                @error('manager_tel')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>


              <div class='bl_officeAccountEdit_inner_content_form_submit c_form_submit'>
                <input type="submit" value="更新">
              </div>

            </form>
          @elseif ($type === 'delete')
            <form action="{{ route('delivery_office.user.update', ['type' => 'delete']) }}" method="POST" class="js_confirm">
              @csrf

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item'>
                <label for="">パスワード</label>
                <input type="password" name="password">
                @error('password')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_item c_form_item'>
                <label for="">確認 パスワード</label>
                <input type="password" name="password_confirmation"></label>
                @error('password_confirmation')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='bl_officeAccountEdit_inner_content_form_submit c_form_submit'>
                <input type="submit" value="退会申請する">
              </div>

            </form>
          @endif

        </div>
      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script src="{{ asset('./js/libs/Functions/form_parts.js') }}"></script>
@endsection
