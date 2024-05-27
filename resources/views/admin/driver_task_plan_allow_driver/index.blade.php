@extends('layouts.admin.app')

@section('title')
  稼働依頼プランで対応可能なドライバープラン一覧
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
          <h2>稼働依頼プランで対応可能なドライバープラン一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">
        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class="el_width4rem">ID</th>
                <th class="el_width3rem">稼働依頼プラン</th>
                <th class="el_width6rem">ドライバープラン</th>
              </tr>

              @foreach ($driver_task_plan_allow_driver_list as $driver_task_plan_allow_driver)
                <tr>
                  <td class='el_center'>
                    {{ $driver_task_plan_allow_driver->id }}
                  </td>
                  <td class='el_center'>
                    {{ $driver_task_plan_allow_driver->joinDriverTaskPlan->name ?? '' }}: {{ $driver_task_plan_allow_driver->driver_task_plan_id ?? '' }}
                  </td>
                  <td class='el_center'>
                    {{ $driver_task_plan_allow_driver->joinDriverkPlan->name ?? '' }}: {{ $driver_task_plan_allow_driver->driver_plan_id ?? '' }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $driver_task_plan_allow_driver_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
