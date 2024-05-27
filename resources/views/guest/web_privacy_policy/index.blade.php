@extends('layouts.guest.app')

@section('title')
  プライバシーポリシー {{ $name ?? '' }}
@endsection

@section('script_head')
@endsection

@section('content')
  <div class="bl_termsServiceIndex">
    <div class="bl_termsServiceIndex_inner">
      <div class="bl_termsServiceIndex_inner_head">
        <div class="bl_termsServiceIndex_inner_head_ttl">
          <h2>プライバシーポリシー {{ $name ?? ''}}<span>/ terms service</span></h2>
        </div>
      </div>
      <section class="bl_termsServiceIndex_inner_content">
        <article class="bl_termsServiceIndex_inner_content_article">
          {!! $privacy_policy ?? '' !!}
        </article>
      </section>
    </div>
  </div>
@endsection


@section('script_bottom')
@endsection
