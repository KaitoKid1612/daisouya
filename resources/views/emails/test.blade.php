@extends('layouts.email.app')


@section('content')
  <p>分類</p>
  <p>姓 名 様</p>
  <p></p>
  {{ $data['msg'] ?? '' }}
@endsection
