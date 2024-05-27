<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width" initial-scale=1>
  <style>
    a.link_btn {
      background: #3c8dbc;
      color: #fff;
      font-size: 18px;
      padding: 8px 18px;
      border-radius: 6px;
    }
    a.link {
      color: #1a0dab;
    }
  </style>
</head>

<body>
  {{-- <header>
    <div style="width:100%; text-align: center;margin-bottom: 64px;">
        <img src="{{ asset('images/common/logo.png') }}" alt="代走屋" style="width:20%;">
    </div>
  </header> --}}
  @yield('content')
  <p>本メールアドレスは配信専用のため返信を受け付けておりません。</p>
  <p>お問い合わせは <a href="{{ route('guest.web_contact.create') }}">こちら</a> から受け付けております。</p>
</body>

</html>
