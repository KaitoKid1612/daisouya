@extends('layouts.admin.app')

@section('title')
  ドライバー一覧
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif


  <div class="bl_index">
    <div class="bl_index_inner">
      <div class="bl_index_inner_head">
        <div class="bl_index_inner_head_ttl">
          <h2>ドライバー登録申請一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        <section class="bl_index_inner_content_formBox">
          <button class="js_show_form_btn bl_index_inner_content_formBox_button">検索フォーム</button>
          <form action="{{ route('admin.register_request_driver.index') }}" method="GET" class="js_form">

            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="keyword">検索ワード</label>
              <input type="text" name='keyword' id="keyword" value="{{ $_GET['keyword'] ?? '' }}" class="el_max_width45rem">
            </div>

            <div class="c_form_submit bl_index_inner_content_formBox_item">
              <input type="submit" value='検索'>
            </div>

            <div class="bl_index_inner_content_formBox_list">
              <div class="bl_index_inner_content_formBox_list_box">
                <h4 class='bl_index_inner_content_formBox_list_ttl'>
                  登録申請ステータス
                </h4>
                <ul>
                  @foreach ($register_request_status_list as $register_request_status)
                    <li class='c_form_checkbox'>
                      <input type="checkbox"
                        name='register_request_status_id[]'
                        value='{{ $register_request_status->id }}'
                        id='register_request_status{{ $register_request_status->id }}'
                        {{ isset($_GET['register_request_status_id']) && in_array($register_request_status->id, $_GET['register_request_status_id']) ? 'checked' : '' }}>
                      <label
                        for="register_request_status{{ $register_request_status->id }}">{{ $register_request_status->name ?? '' }}
                      </label>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>
            <div class="c_form_item el_width12rem bl_index_inner_content_formBox_item">
              <label for="addr1_id">都道府県</label>
              <div class="c_form_select">
                <select name="addr1_id" id="addr1_id">
                  <option disabled selected>
                    選択してください。
                  </option>
                  <option value=''>
                    指定なし
                  </option>
                  @foreach ($prefecture_list as $prefecture)
                    <option value="{{ $prefecture->id }}" {{ isset($_GET['addr1_id']) && $prefecture->id == $_GET['addr1_id'] ? 'selected' : '' }}>
                      {{ $prefecture->name ?? '' }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>


            <div class="c_form_item el_width12rem bl_index_inner_content_formBox_item">
              <label for="orderby">並び順</label>
              <div class="c_form_select">
                <select name="orderby" id="orderby">
                  <option disabled selected>
                    選択してください。
                  </option>
                  <option value=''>
                    指定なし
                  </option>
                  @foreach ($orderby_list as $orderby)
                    <option value="{{ $orderby['value'] }}"
                      {{ isset($_GET['orderby']) && $_GET['orderby'] == $orderby['value'] ? 'selected' : '' }}>
                      {{ $orderby['text'] }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="c_form_submit bl_index_inner_content_formBox_item">
              <input type="submit" value='検索'>
            </div>


            <div class="c_reset_btn_box bl_index_inner_content_formBox_item">
              <button class="js_reset_form_btn">フォームリセット</button>
            </div>

          </form>
        </section>



        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class='el_width3rem'>ID</th>
                <th class='el_width5rem'>ステータス</th>
                <th class='el_width5_5rem'>ドライバープラン</th>
                <th class='el_width12rem'>名前</th>
                <th class='el_width12rem'>email</th>
                <th class='el_width18rem'>住所</th>
                <th class='el_width6rem'>電話番号</th>
                <th class='el_width8rem'>作成日</th>
              </tr>

              @foreach ($register_request_list as $register)
                <tr>
                  <td class='el_center'>
                    <a href="{{ route('admin.register_request_driver.show', ['register_request_id' => $register->id]) }}"
                      class="c_btn el_btn">{{ $register->id ?? '' }}</a>
                  </td>
                  <td class="el_center">{{ $register->get_register_request_status->name ?? '' }}
                  </td>
                  <td class="el_center">{{ $register->joinDriverPlan->name ?? 'データなし' }}
                  </td>
                  <td class="el_font0_8rem">{{ $register->full_name ?? '' }}</td>
                  <td class="el_font0_8rem">{{ $register->email ?? '' }}</td>
                  <td class="el_font0_7rem">{{ $register->full_addr ?? '' }}</td>
                  <td class="el_font0_8rem">{{ $register->tel ?? '' }}</td>
                  <td class="el_font0_7rem">{{ $register->created_at ?? '' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $register_request_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
