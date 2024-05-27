@extends('layouts.email.app')

@section('content')
  <p>システムレポート</p>
  <pre>
    <code>
{{ $data['result'] ?? 'レポート結果がありません' }}
    </code>
  </pre>
@endsection
