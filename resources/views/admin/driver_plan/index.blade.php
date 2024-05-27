@extends('layouts.admin.app')

@section('title')
  稼働依頼プラン一覧
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
          <h2>ドライバープラン一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">
        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class="el_width1rem">ID</th>
                <th class="el_width1rem">プラン名</th>
                <th class="el_width1rem">ラベル</th>
              </tr>

              @foreach ($driver_plan_list as $driver_plan)
                <tr>
                  <td class='el_center'>{{ $driver_plan->id ?? '' }}</td>
                  <td>{{ $driver_plan->name ?? '' }}</td>
                  <td>{{ $driver_plan->label ?? '' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $driver_plan_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
