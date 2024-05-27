@extends('layouts.admin.app')

@section('title')
  管理者一覧
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
          <h2>管理者一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        <div class="bl_index_inner_content_handle">
          <a href="{{ route('admin.user.create') }}" class="c_btn">作成</a>
        </div>


        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class='el_width3rem'>ID</th>
                <th class='el_width12rem'>名前</th>
                <th class='el_width12rem'>メールアドレス</th>
                <th class='el_width4rem'>権限</th>
                <th class='el_width11rem'>作成日</th>
                <th class='el_width11rem'>更新日</th>
                <th class='el_width4rem'>編集</th>
                <th class='el_width4rem'> 削除</th>
              </tr>

              @foreach ($admin_list as $admin)
                <tr>
                  <td class='el_center'>{{ $admin->id }}</td>
                  <td>{{ $admin->name ?? '' }}</td>
                  <td>{{ $admin->email }}</td>
                  <td>{{ $admin->joinAdminPermissionGroup->name ?? '' }}</td>
                  <td>{{ $admin->created_at }}</td>
                  <td>{{ $admin->updated_at }}</td>
                  <td class='el_center'><a
                      href="{{ route('admin.user.edit', ['admin_id' => $admin->id]) }}"
                      class='c_btn el_btn'>編集</a>
                  </td>
                  <td class='el_center'>
                    <form action="{{ route('admin.user.destroy', ['admin_id' => $admin->id]) }}" method="POST" class="js_confirm">
                      @csrf
                      <input type="submit" value="削除" class='c_btn el_btn el_bg_red'>
                    </form>
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
