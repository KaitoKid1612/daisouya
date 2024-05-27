@extends('layouts.admin.app')

@section('title')
  退会申請一覧(依頼者)
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
          <h2>退会申請一覧(依頼者)</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">
        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class='el_width4rem'>ID</th>
                <th class='el_width4rem'>配送会社</th>
                <th class='el_width9rem'>営業所名</th>
                <th class='el_width11rem'>email</th>
                <th class='el_width11rem'>申請日</th>
                <th class='el_width4rem'>編集</th>
              </tr>

              @foreach ($office_list_unsubscribe as $office)
                <tr>
                  <td class='el_center'>
                    <a href="#" class="c_btn el_btn">
                      {{ $office->id }}
                    </a>
                  </td>
                  <td>
                    @if (isset($office->delivery_company_id) && $office->delivery_company_id)
                      <a
                        href="{{ route('admin.delivery_company.show', ['company_id' => $office->delivery_company_id]) }}">{{ $office->joinCompany->name  ?? '' }}
                      </a>
                    @else 
                      {{ $office->delivery_company_name ?? '' }}
                    @endif
                  </td>
                  <td>{{ $office->name ?? '' }}</td>
                  <td>{{ $office->email }} </td>
                  <td>{{ $office->deleted_at }}</td>
                  <td class='el_center'>
                    <a href="{{ route('admin.delivery_office.edit', ['office_id' => $office->id]) }}" class='c_btn el_btn'>
                      編集
                    </a>
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
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
