@extends('layouts.admin.app')

@section('title')
  ドライバー登録営業所 作成
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  <div class="bl_create">
    <div class="bl_create_inner">
      <div class="bl_create_inner_head">
        <div class="bl_create_inner_head_ttl">
          <h2>ドライバー登録営業所 作成</h2>
        </div>
      </div>

      <div class="bl_create_inner_content">
        <section class="bl_create_inner_content_data">
          <form
            action="{{ route('admin.driver_register_delivery_office.store') }}"method="POST" class="js_confirm">
            @csrf

            <div class="bl_create_inner_content_data_form">


              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="driver_id">ドライバーID</label>
                <input type="number" name='driver_id' id='driver_id' value='{{ old('driver_id', $_GET['driver_id'] ?? '') }}' {{ isset($_GET['driver_id']) ? 'readonly' : '' }}>
                <p class="el_error_msg">
                  @error('driver_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="bl_create_inner_content_data_form_multi_list">
                <p class="el_error_msg">
                  @error('delivery_office_id')
                    {{ $message }}
                  @enderror
                </p>
                @foreach ($delivery_multi_list as $delivery_list)
                  <div class="bl_create_inner_content_data_form_multi_list_sec">
                    <h4 class='bl_create_inner_content_data_form_multi_list_ttl'>
                      {{ $delivery_list['company']['name'] }}
                    </h4>
                    <ul>
                      @foreach ($delivery_list['office_list'] as $office)
                        <li class='c_form_checkbox'>
                          <input type="checkbox"
                            name='delivery_office_id[]'
                            value='{{ $office['id'] }}'
                            id='{{ $delivery_list['company']['name'] }}_{{ $office['name'] }}'>
                          <label
                            for="{{ $delivery_list['company']['name'] }}_{{ $office['name'] }}">{{ $office['name'] }}
                          </label>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                @endforeach
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item el_submit">
                <input type="submit" value="作成" class='c_btn'>
              </div>

            </div>
          </form>
        </section>
      </div>
    </div>
  </div>

@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
