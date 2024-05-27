@extends('layouts.driver.app')

@section('title')
  403稼働依頼詳細
@endsection

@section('content')
  <div class="bl_taskShow">
    <div class="bl_taskShow_inner">
      <div class="bl_taskShow_inner_head">
        <div class="bl_taskShow_inner_head_ttl">
          <h2>稼働依頼詳細<span>/ request details</span></h2>
        </div>
      </div>
      <div class="bl_taskShow_inner_content">
        <p class="bl_taskShow_inner_content_message403">審査中のため、稼働依頼詳細を閲覧できません。<br>
           審査と登録が完了次第、閲覧が可能となります。</p>
      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script src="{{ asset('./js/libs/Functions/form_parts.js') }}"></script>
@endsection
