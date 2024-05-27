@extends('layouts.delivery_office.app')

@section('title')
  ご依頼履歴
@endsection

@section('content')
  {{-- メッセージ --}}
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  <div class="bl_taskTemplateIndex">
    <div class="bl_taskTemplateIndex_inner">
      <form action="{{ route('delivery_office.driver_task_template.index') }}" method="GET">
        <div class="bl_taskTemplateIndex_inner_head">
          <div class="bl_taskTemplateIndex_inner_head_ttl">
            <h2>保存した依頼一覧<span>/ driver task template</span></h2>
          </div>
          <div class="bl_taskTemplateIndex_inner_head_option">
            <select name="orderby" id="orderby">
              <option disabled selected>
                並び順
              </option>
              <option value="1" {{ (isset($_GET['orderby']) && $_GET['orderby']) === '1' ? 'selected' : '' }}>
                指定なし
              </option>
              <option value="2" {{ (isset($_GET['orderby']) && $_GET['orderby']) === '2' ? 'selected' : '' }}>
                稼働日順
              </option>
              <option value="3" {{ (isset($_GET['orderby']) && $_GET['orderby']) === '3' ? 'selected' : '' }}>
                作成日順
              </option>
            </select>
          </div>
          <div class="bl_taskTemplateIndex_inner_head_submit">
            <input type="submit" value="検索">
          </div>
        </div>
      </form>

      <div class="bl_taskTemplateIndex_inner_content">
        <ul class="bl_taskTemplateIndex_inner_content_list">
          @if (count($task_templates) !== 0)
            @foreach ($task_templates as $key => $template)
              <li class="bl_taskTemplateIndex_inner_content_item">
                <div class="bl_taskTemplateIndex_inner_content_item_left">
                  <div class="bl_taskTemplateIndex_inner_content_item_left_line">
                    <span>保存日</span>
                    <span>{{ $template->created_at }}</span>
                  </div>
                  <div class="bl_taskTemplateIndex_inner_content_item_left_line">
                    <span>集荷先</span>
                    <span>{{ $template->task_delivery_company_name . ' ' . $template->task_delivery_office_name }}</span>
                  </div>
                  <div class="bl_taskTemplateIndex_inner_content_item_left_line">
                    <span>担当ドライバー</span>
                    <span>{{ $template->joinDriver->name_mei ?? '' }}</span>
                  </div>
                  <div class="bl_taskTemplateIndex_inner_content_item_left_line">
                    <span>稼働依頼プラン</span>
                    <span>{{ $template->driver_task_plan_id }}</span>
                  </div>
                </div>
                <div class="bl_taskTemplateIndex_inner_content_item_right">
                  <a class="detail" href="/delivery-office/driver-task/create?template={{ $template->id }}">詳細</a>
                  <a class="delete delete_{{$template->id}}" data-template_id="{{ $template->id }}">削除</a>
                </div>
              </li>

              <form class="bl_taskTemplateIndex_inner_form bl_taskTemplateIndex_inner_form_{{$template->id}}" method="POST">
                @csrf
                <p>この依頼を削除してもよろしいですか？</p>

                <div class="bl_taskTemplateIndex_inner_form_action">
                  <button type="button" class="bl_taskTemplateIndex_inner_form_action_close_{{$template->id}}">キャンセル</button>
                  <button type="submit" class="bl_taskTemplateIndex_inner_form_action_confirm_{{$template->id}}">OK</button>
                </div>
              </form>
            @endforeach
          @else
            <div class="bl_taskTemplateIndex_inner_content_list_empty">保存された依頼はありません。</div>
          @endif
        </ul>
      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
       const listButtonDelete = document.querySelectorAll('.delete');

       if (listButtonDelete) {
        listButtonDelete.forEach(button => {
          const template_id = button.dataset.template_id;

          // Elements
          const form = document.querySelector(`.bl_taskTemplateIndex_inner_form_${template_id}`);
          const buttonDelete = document.querySelector(`.delete_${template_id}`);
          const buttonClose = document.querySelector(`.bl_taskTemplateIndex_inner_form_action_close_${template_id}`)
          const buttonConfirm = document.querySelector(`.bl_taskTemplateIndex_inner_form_action_confirm_${template_id}`)

          buttonDelete.addEventListener('click', (e) => {
            form.classList.add('show');
          })

          buttonClose.addEventListener('click', (e) => {
            form.classList.remove('show');
          })

          buttonConfirm.addEventListener('click', (e) => {
            console.log(template_id);

            form.action = '{{ route("delivery_office.driver_task_template.delete") }}/' + template_id;
            form.submit();
          })
        })
       }
    });
  </script>
@endsection
