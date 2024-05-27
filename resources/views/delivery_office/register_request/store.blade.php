@extends('layouts.delivery_office.app')

@section('title')
  登録申請
@endsection

@section('content')
  <div class="bl_registerRequestStore">
    <div class="bl_registerRequestStore_inner">
      <div class="bl_registerRequestStore_inner_head">
        <div class="bl_registerRequestStore_inner_head_ttl">
          <h2>依頼者 登録申請<span>/ register request delivery office </span></h2>
        </div>
      </div>
      <div class="bl_registerRequestStore_inner_content">
        <section class="bl_registerRequestStore_inner_content_msg">
          <p class="el_red">
            {{ session('msg') ?? '' }}
          </p>
          <p>依頼者登録申請ID:
            @if (session('msg'))
              {{ session('id') ?? 'なし' }}
            @endif
          </p>
        </section>
      </div>
    </div>
  </div>
@endsection
