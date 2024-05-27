@extends('layouts.admin.app')

@section('title')
  配送会社 詳細
@endsection

@section('content')

  @if ($company)
    <div class="bl_show">
      <div class="bl_show_inner">
        <div class="bl_show_inner_head">
          <div class="bl_show_inner_head_ttl">
            <h2>配送会社 詳細</h2>
          </div>
        </div>

        <div class="bl_show_inner_content">
          <div class="bl_show_inner_content_handle">
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.delivery_company.edit', ['company_id' => $company->id]) }}" class="c_btn">編集</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <form action="{{ route('admin.delivery_company.destroy', ['company_id' => $company->id]) }}" method="POST" class="js_confirm">
                @csrf
                <input type="submit" value="削除" class="c_btn el_bg_red">
              </form>
            </div>
          </div>
          <section class="bl_show_inner_content_data">
            <dl>
              <dt>ID</dt>
              <dd>{{ $company->id ?? ''  }}</dd>
            </dl>

            <dl>
              <dt>名前</dt>
              <dd>{{ $company->name ?? '' }}</a>
              </dd>
            </dl>

            <dl>
              <dt>作成日</dt>
              <dd>{{ $company->created_at ?? '' }}</dd>
            </dl>

            <dl>
              <dt>更新日</dt>
              <dd>{{ $company->updated_at ?? '' }}</dd>
            </dl>
          </section>

          <section class="bl_show_inner_content_relationData">
            <h3 class="bl_show_inner_content_relationData_ttl">所属営業所一覧</h3>
            <div class='bl_show_inner_content_relationData_tableBox'>
              <table>
                <tbody>
                  <tr>
                    <th class='el_width4rem'>ID</th>
                    <th class='el_width12rem'>営業所</th>
                    <th class='el_width12rem'>担当者</th>
                    <th class='el_width14rem'>メールアドレス</th>
                    <th class='el_width11rem'>作成日</th>
                    <th class='el_width11rem'>更新日</th>
                  </tr>

                  @foreach ($company->joinOffice as $office)
                    <tr>
                    <tr>
                      <td class='ec_center'><a
                          href="{{ route('admin.delivery_office.show', ['office_id' => $office->id]) }}"
                          class="c_btn">{{ $office->id }}</a></td>
                      <td>{{ $office->name  ?? '' }}</td>
                      <td>{{ $office->full_name ?? '' }}</td>
                      <td>{{ $office->email ?? '' }}</td>
                      <td>{{ $office->created_at ?? '' }}</td>
                      <td>{{ $office->updated_at ?? '' }}</td>
                    </tr>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </section>
        </div>
      </div>
    </div>
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
