@extends('layouts.admin.app')

@section('title')
  ドライバー登録営業所一覧
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
          <h2>ドライバー登録営業所一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        {{-- <div class="bl_index_inner_content_handle">
          <a href="{{ route('admin.driver_register_delivery_office.create') }}" class="c_btn">作成</a>
        </div> --}}


        <section class="bl_index_inner_content_formBox">
          <button class="js_show_form_btn bl_index_inner_content_formBox_button">検索フォーム</button>
          <form action="{{ route('admin.driver_register_delivery_office.index') }}" method="GET" class="js_form">

            <input type="hidden" name="driver_id" value="{{ $_GET['driver_id'] ?? '' }}">
            <input type="hidden" name="delivery_office_id" value="{{ $_GET['delivery_office_id'] ?? '' }}">

            {{-- <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="keyword">検索ワード</label>
              <input type="text" name='keyword' id="keyword" value="{{ isset($_GET['keyword']) ? $_GET['keyword'] : '' }}">
            </div>

            <div class="c_form_submit bl_index_inner_content_formBox_item">
              <input type="submit" value='検索'>
            </div> --}}



            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_created_at">作成日</label>
              <input type="date" name='from_created_at' id="from_created_at"
                value="{{ $_GET['from_created_at'] ?? '' }}" class='el_width12rem'>
              <span>-</span>
              <input type="date" name='to_created_at'
                value="{{ $_GET['to_created_at'] ?? '' }}" class='el_width12rem'>
            </div>


            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_updated_at">更新日</label>
              <input type="date" name='from_updated_at' id="from_updated_at"
                value="{{ $_GET['from_updated_at'] ?? '' }}" class='el_width12rem'>
              <span>-</span>

              <input type="date" name='to_updated_at'
                value="{{ $_GET['to_updated_at'] ?? '' }}" class='el_width12rem'>
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
                <th class='el_width12rem'>ドライバー</th>
                <th class='el_width12rem'>営業所</th>
                <th class='el_width11rem'>作成日</th>
                <th class='el_width11rem'>更新日</th>
                <th class='el_width4rem'>削除</th>
              </tr>

              @foreach ($register_office_list as $register_office)
                <tr>
                  <td class='el_center'>{{ $register_office->id }}</td>
                  <td>
                    <a
                      href="{{ route('admin.driver.show', ['driver_id' => $register_office->driver_id]) }}" class='el_link'>
                      {{ $register_office->joinDriver->full_name ?? 'データなしorソフト削除済み' }}
                    </a>
                  </td>
                  <td>
                    <a
                      href="{{ route('admin.delivery_office.show', ['office_id' => $register_office->delivery_office_id]) }}" class='el_link'>{{ $register_office->joinOffice->name ?? 'データなしorソフト削除済み' }}
                    </a>
                  </td>
                  <td>{{ $register_office->created_at }}</td>
                  <td>{{ $register_office->updated_at }}</td>
                  <td class='el_center'>
                    <form action="{{ route('admin.driver_register_delivery_office.destroy', ['register_office_id' => $register_office->id]) }}" method="POST" class="js_confirm">
                      @csrf
                      <input type="hidden" name="type" value="force">
                      <input type="submit" value="削除" class="c_btn el_btn el_bg_red">
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $register_office_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
