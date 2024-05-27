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
          <h2>ドライバー一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">
        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class='el_width4rem'>ID</th>
                <th class='el_width4rem'>ドライバープラン</th>
                <th class='el_width9rem'>名前</th>
                <th class='el_width11rem'>email</th>
                <th class='el_width11rem'>申請日</th>
                <th class='el_width4rem'>編集</th>
              </tr>

              @foreach ($drivers as $driver)
                <tr>
                  <td class='el_center'>
                    <a href="#" class="c_btn el_btn">
                      {{ $driver->id }}
                    </a>
                  </td>
                  <td>{{ $driver->joinDriverPlan->name ?? 'データなし' }}</td>
                  <td>{{ $driver->full_name }}</td>
                  <td>{{ $driver->email }} </td>
                  <td>{{ $driver->deleted_at }}</td>
                  <td class='el_center'>
                    <a href="{{ route('admin.driver.edit', ['driver_id' => $driver->id]) }}" class='c_btn el_btn'>
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
