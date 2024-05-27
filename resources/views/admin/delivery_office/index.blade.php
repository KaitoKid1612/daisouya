@extends('layouts.admin.app')

@section('title')
  営業所一覧
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
          <h2>依頼者一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        <div class="bl_index_inner_content_handle">
          <a href="{{ route('admin.delivery_office.create') }}" class="c_btn">作成</a>
        </div>

        <section class="bl_index_inner_content_formBox">
          <button class="js_show_form_btn bl_index_inner_content_formBox_button">検索フォーム</button>
          <form action="{{ route('admin.delivery_office.index') }}" method="GET" class="js_form">

            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="keyword">検索ワード</label>
              <input type="text" name='keyword' id="keyword" value="{{ $_GET['keyword'] ?? '' }}" class="el_max_width45rem">
            </div>

            <div class="c_form_submit bl_index_inner_content_formBox_item">
              <input type="submit" value='検索'>
            </div>

            <div class="c_form_item el_width12rem bl_index_inner_content_formBox_item">
              <label for="addr1_id">都道府県</label>
              <select name="addr1_id" id="addr1_id">
                <option disabled selected>
                  選択してください。
                </option>
                <option value=''>
                  指定なし
                </option>
                @foreach ($prefecture_list as $prefecture)
                  <option value="{{ $prefecture->id }}"
                    {{ isset($_GET['addr1_id']) && $_GET['addr1_id'] == $prefecture['id'] ? 'selected' : '' }}>
                    {{ $prefecture['name'] }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="c_form_item bl_index_inner_content_formBox_item">
              <label for="from_task_count">稼働数</label>

              <input type="number" name="from_task_count" id="from_task_count" min="0"
                value="{{ $_GET['from_task_count'] ?? '' }}" class='el_width12rem'>

              <span>-</span>

              <input type="number" name="to_task_count" id="" min="0"
                value="{{ $_GET['to_task_count'] ?? '' }}" class='el_width12rem'>
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
                <th class='el_width4rem'>削除状態</th>
                <th class='el_width9rem'>配送会社</th>
                <th class='el_width11rem'>営業所名</th>
                <th class='el_width11rem'>担当者</th>
                <th class='el_width11rem'>email</th>
                <th class='el_width4rem'>依頼数</th>
                <th class='el_width11rem'>作成日</th>
                <th class='el_width4rem'>編集</th>
              </tr>

              @foreach ($office_list as $office)
                <tr>
                  <td class='el_center'>
                    <a
                      href="{{ route('admin.delivery_office.show', ['office_id' => $office->id]) }}"
                      class="c_btn el_btn">
                      {{ $office->id }}
                    </a>
                  </td>
                  <td>{{ $office->trashed() ? 'ソフト削除' : '' }}</td>
                  <td>
                    @if (isset($office->delivery_company_id) && $office->delivery_company_id)
                      <a
                        href="{{ route('admin.delivery_company.show', ['company_id' => $office->delivery_company_id]) }}">{{ $office->joinCompany->name  ?? '' }}
                      </a>
                    @else 
                      {{ $office->delivery_company_name ?? '' }}
                    @endif
                  </td>
                  <td>
                    {{ $office->name ?? '' }}
                  </td>
                  <td>{{ $office->manager_name_sei ?? '' }} {{ $office->manager_name_mei ?? '' }}</td>
                  <td>{{ $office->email ?? '' }}</td>
                  <td>{{ $office->join_task_count ?? '' }}</td>
                  <td>{{ $office->created_at ?? '' }}</td>
                  <td class='el_center'><a href="{{ route('admin.delivery_office.edit', ['office_id' => $office->id]) }}"
                      class='c_btn el_btn'>編集</a>
                  </td>
                  {{-- <td>{{ $office->updated_at }}</td> --}}
                  {{-- <td>
                    <form action="{{ route('admin.delivery_office.destroy', ['office_id' => $office->id]) }}"
                      method="POST">
                      @csrf
                      <input type="submit" value="削除">
                    </form>
                  </td> --}}
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $office_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
