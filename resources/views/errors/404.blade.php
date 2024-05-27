@section('code', '404')
@section('message', __('Not Found'))

@extends('errors.layout')

@section('title')
  Not Found
@endsection

@section('content')
    <section class="bl_noData">
      <div class="bl_noData_inner">
        <p>
          このページは存在しません。
        </p>
      </div>
    </section>
@endsection
