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
          <h2>稼働依頼プラン一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">
        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class='el_width1rem'>編集</th>
                <th class="el_width1rem">ID</th>
                <th class="el_width1rem">プラン名</th>
                <th class="el_width1rem">ラベル</th>
                <th class="el_width2rem">システム利用料金</th>
                <th class="el_width3rem">システム利用料金(運賃の%)</th>
                <th class="el_width2rem">システム料金(繁忙期)</th>
                <th class="el_width3rem">システム料金(繁忙期,運賃の%)</th>
                <th class="el_width3rem">システム料金(繁忙期,運賃の%,既定運賃以上の場合)</th>
                <th class="el_width1rem">緊急依頼料金</th>
              </tr>

              @foreach ($driver_task_plan_list as $driver_task_plan)
                <tr>
                  <td class='el_center'><a href="{{ route('admin.driver_task_plan.edit', ['driver_task_plan_id' => $driver_task_plan->id]) }}"
                      class='c_link el_btn'>編集</a>
                  </td>
                  <td class='el_center'>{{ $driver_task_plan->id ?? '' }}</td>
                  <td>{{ $driver_task_plan->name ?? '' }}</td>
                  <td>{{ $driver_task_plan->label ?? '' }}</td>
                  <td>{{ $driver_task_plan->system_price ?? '' }}</td>
                  <td>{{ $driver_task_plan->system_price_percent ?? '' }}</td>
                  <td>{{ $driver_task_plan->busy_system_price ?? '' }}</td>
                  <td>{{ $driver_task_plan->busy_system_price_percent ?? '' }}</td>
                  <td>{{ $driver_task_plan->busy_system_price_percent_over ?? '' }}</td>
                  <td>{{ $driver_task_plan->emergency_price ?? '' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>
        {{ $driver_task_plan_list->links('parts.pagination') }}
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
