@extends('layouts.admin.app')

@section('title')
  システム情報 phpinfo
@endsection

@section('content')
  <div class="bl_index">
    <div class="bl_index_inner">
      @php
        phpinfo();
      @endphp
    </div>
  </div>
@endsection
