@extends('layouts.admin.app')

@section('title')
  お問い合わせ一覧
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
          <h2>お問い合わせ 一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        <section class="bl_index_inner_content_formBox">
          <button class="js_show_form_btn bl_index_inner_content_formBox_button">検索フォーム</button>
          <form action="{{ route('admin.web_contact.index') }}" method="GET" class="js_form">

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
                  ユーザータイプ
                </h4>
                <ul>
                  @foreach ($web_contact_status_list as $web_contact_status)
                    <li class='c_form_checkbox'>
                      <input type="checkbox"
                        name='web_contact_status_id[]'
                        value='{{ $web_contact_status->id }}'
                        id='web_contact_status{{ $web_contact_status->id }}'
                        {{ isset($_GET['web_contact_status_id']) && in_array($web_contact_status->id, $_GET['web_contact_status_id']) ? 'checked' : '' }}>
                      <label
                        for="web_contact_status{{ $web_contact_status->id }}">{{ $web_contact_status->name ?? '' }}
                      </label>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>

            <div class="bl_index_inner_content_formBox_list">
              <div class="bl_index_inner_content_formBox_list_box">
                <h4 class='bl_index_inner_content_formBox_list_ttl'>
                  稼働ステータス
                </h4>
                <ul>
                  @foreach ($user_type_list as $user_type)
                    <li class='c_form_checkbox'>
                      <input type="checkbox"
                        name='user_type_id[]'
                        value='{{ $user_type->id }}'
                        id='user_type{{ $user_type->id }}'
                        {{ isset($_GET['user_type_id']) && in_array($user_type->id, $_GET['user_type_id']) ? 'checked' : '' }}>
                      <label
                        for="user_type{{ $user_type->id }}">{{ $user_type->name ?? '' }}
                      </label>
                    </li>
                  @endforeach
                </ul>
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
                <th class='el_width4rem'>ID</th>
                <th class='el_width6rem'>ステータス</th>
                <th class='el_width7rem'>ユーザータイプ</th>
                <th class='el_width10rem'>名前</th>
                <th class='el_width12rem'>メールアドレス</th>
                <th class='el_width8rem'>電話番号</th>
                <th class='el_width8rem'>お問い合わせタイプ</th>
                <th class='el_width12rem'>タイトル</th>
                <th class='el_width9rem'>作成日</th>
              </tr>

              @foreach ($web_contact_list as $web_contact)
                <tr>
                  <td class='el_center'>
                    <a href="{{ route('admin.web_contact.show', ['contact_id' => $web_contact->id]) }}"
                      class="c_btn el_btn">{{ $web_contact->id }}</a>
                  </td>
                  <td class='el_center'>{{ $web_contact->get_web_contact_status->name ?? '' }}</td>
                  <td class='el_center'>{{ $web_contact->joinUserType->name ?? '' }}</td>
                  <td>{{ $web_contact->full_name ?? '' }}</td>
                  <td>{{ $web_contact->email ?? '' }}</td>
                  <td>{{ $web_contact->tel ?? '' }}</td>
                  <td class='el_center'>{{ $web_contact->get_web_contact_type->name ?? '' }}</td>
                  <td class='el_font0_8rem'>{{ $web_contact->title ?? '' }}</td>
                  <td class='el_font0_8rem'>{{ $web_contact->created_at ?? '' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $web_contact_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
