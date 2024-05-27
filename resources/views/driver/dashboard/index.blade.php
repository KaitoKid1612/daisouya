@extends('layouts.driver.app')

@section('title')
  ダッシュボード
@endsection

@section('content')
  <div class="bl_dashboard">
    <div class="bl_dashboard_inner">
      @foreach ($link_list as $link)
        @if (checkDriverAccessFilter($link->href))
          <div class="bl_dashboard_inner_link">
            <a href="{{ $link->href }}">{{ $link->text }}</a>
          </div>
        @endif
      @endforeach
    </div>
  </div>
@endsection
