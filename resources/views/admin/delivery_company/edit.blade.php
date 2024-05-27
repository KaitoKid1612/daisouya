@extends('layouts.admin.app')

@section('title')
  配送会社 編集
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  @if ($company)
    <div class="bl_edit">
      <div class="bl_edit_inner">
        <div class="bl_edit_inner_head">
          <div class="bl_edit_inner_head_ttl">
            <h2>配送会社 編集</h2>
          </div>
        </div>

        <div class="bl_edit_inner_content">
          <section class="bl_edit_inner_content_data">
            <form action="{{ route('admin.delivery_company.update', ['company_id' => $company->id]) }}" method="POST">
              @csrf
              <div class="bl_edit_inner_content_data_form">

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="name">会社名</label>
                  <input type="text" name='name' id='name' value="{{ old('name', $company->name) }}">
                  <p class="el_error_msg">
                    @error('name')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item el_submit">
                  <input type="submit" value="編集" class='c_btn'>
                </div>
            </form>
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
