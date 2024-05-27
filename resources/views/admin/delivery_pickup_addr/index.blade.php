@extends('layouts.admin.app')

@section('title')
  営業所 集荷先住所一覧
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
          <h2>営業所 集荷先住所一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        <div class="bl_index_inner_content_handle">
          <a href="{{ route('admin.delivery_pickup_addr.create') }}" class="c_btn bl_index_inner_content_handle_item">作成</a>
        </div>

        <section class="bl_index_inner_content_formBox">
          <button class="js_show_form_btn bl_index_inner_content_formBox_button">検索フォーム</button>
          <form action="{{ route('admin.delivery_pickup_addr.index') }}" method="GET" class="js_form">
            <input type="hidden" name="delivery_office_id" value="{{ $_GET['delivery_office_id'] ?? '' }}">


            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="keyword">検索ワード</label>
              <input type="text" name='keyword' id="keyword" value="{{ $_GET['keyword'] ?? '' }}"
                class="el_max_width45rem">
            </div>

            <div class="c_form_submit bl_index_inner_content_formBox_item">
              <input type="submit" value='検索'>
            </div>

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
                <th class='el_width4rem'>ID</th>
                <th class='el_width12rem'>依頼者ユーザ</th>
                <th class='el_width24rem'>集荷先</th>
                <th class='el_width9rem'>作成日</th>
                <th class='el_width4rem'>編集</th>
              </tr>

              @foreach ($pickup_addr_list as $pickup_addr)
                <tr>
                  <td class='el_center'><a
                    href="{{ route('admin.delivery_pickup_addr.show', ['pickup_id' => $pickup_addr->id]) }}"
                    class='c_link el_btn'>{{ $pickup_addr->id }}</a></td>
                  <td class="el_font0_8rem">
                    <a
                      href="{{ route('admin.delivery_office.show', ['office_id' => $pickup_addr->delivery_office_id]) }}" class="c_normal_link">
                      {{ $pickup_addr->joinOffice->name ?? 'データなしorソフト削除済み' }}
                    </a>
                  </td>
                  <td class="el_font0_8rem">{{ $pickup_addr->full_post_code ?? '' }}
                    {{ $pickup_addr->full_addr ?? '' }}</td>
                  <td class="el_font0_8rem">{{ $pickup_addr->created_at }}</td>
                  <td class='el_center'><a
                      href="{{ route('admin.delivery_pickup_addr.edit', ['pickup_id' => $pickup_addr->id]) }}"
                      class='c_link el_btn'>編集</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $pickup_addr_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
