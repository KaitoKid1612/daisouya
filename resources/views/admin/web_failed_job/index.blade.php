@extends('layouts.admin.app')

@section('title')
  Redis
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
          <h2>Redis 一覧</h2>
        </div>
      </div>
      <div class="bl_index_inner_content">

        <section class="bl_index_inner_content_data">
          <table>
            <tbody>
              <tr>
                <th class='el_width2rem'></th>
                <th class='el_width2rem'>id</th>
                <th class='el_width4rem'>uuid</th>
                <th class='el_width4rem'>connection</th>
                <th class='el_width4rem'>queue</th>
                <th class='el_width17rem'>exception</th>
                <th class='el_width5rem'>failed_at</th>
              </tr>

              @foreach ($failed_job_list as $failed_job_item)
                <tr>
                  <td class='el_center'><a
                      href="{{ route('admin.web_failed_job.show', ['failed_job_id' => $failed_job_item->id]) }}"class='c_link el_btn'>詳細</a></td>
                  <td>{{ $failed_job_item->id ?? '' }}</td>
                  <td>{{ $failed_job_item->uuid ?? '' }}</td>
                  <td>{{ $failed_job_item->connection ?? '' }}</td>
                  <td>{{ $failed_job_item->queue ?? '' }}</td>
                  <td>{{ $failed_job_item->exception ?? '' }}</td>
                  <td>{{ $failed_job_item->failed_at ?? '' }}</td>
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
