@extends('layouts.guest.app')

@section('title')
  お問い合わせ
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  <div class="bl_contactCreate">
    <div class="bl_contactCreate_inner">
      <div class="bl_contactCreate_inner_head">
        <div class="bl_contactCreate_inner_head_ttl">
          <h2>お問い合わせ 完了 お問合せID: {{ session('web_contact')->id ?? '' }}</h2>
        </div>
      </div>

      <div class="bl_contactCreate_inner_content">
        <div class="bl_contactCreate_inner_content_done">
          <p><a href="{{ route('driver.login') }}" class="c_normal_link">ドライバーはこちら</a></p>
          <p><a href="{{ route('delivery_office.login') }}" class="c_normal_link">依頼者はこちら</a></p>
        </div>
      </div>
    </div>
  </div>
@endsection
