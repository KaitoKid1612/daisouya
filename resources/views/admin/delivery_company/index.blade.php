@extends('layouts.admin.app')

@section('title')
  稼働依頼一覧
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
          <h2>配送会社一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        <div class="bl_index_inner_content_handle">
          <a href="{{ route('admin.delivery_company.create') }}" class="c_btn">作成</a>
        </div>

        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class='el_width4rem'>ID</th>
                <th class='el_width12rem'>名前</th>
                <th class='el_width11rem'>作成日</th>
                <th class='el_width11rem'>更新日</th>
                <th class='el_width4rem'>編集</th>
              </tr>

              @foreach ($company_list as $company)
                <tr>
                  <td class='el_center'>
                    <a href="{{ route('admin.delivery_company.show', ['company_id' => $company->id]) }}"
                      class="c_btn el_btn">{{ $company->id }}</a>
                  </td>
                  <td>{{ $company->name  ?? '' }}</td>
                  <td>{{ $company->created_at }}</td>
                  <td>{{ $company->updated_at }}</td>
                  <td class='el_center'><a
                      href="{{ route('admin.delivery_company.edit', ['company_id' => $company->id]) }}"
                      class='c_btn el_btn'>編集</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
      </div>
    </div>
  </div>
@endsection
