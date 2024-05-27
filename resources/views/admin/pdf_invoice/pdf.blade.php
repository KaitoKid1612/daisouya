<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }} 管理画面</title>
  <meta name="viewport" content="width=device-width" initial-scale=1>
  <title>PDF</title>
  <link rel="stylesheet" href="{{ asset('css/app_admin.css') }}">
  <style>
    @font-face {
      font-family: GenShinGothic;
      font-style: normal;
      font-weight: normal;
      src: url('{{ public_path('/fonts/GenShinGothic-Regular-lightweight.ttf') }}');
    }

    @font-face {
      font-family: GenShinGothic;
      font-weight: bold;
      src: url('{{ public_path('/fonts/GenShinGothic-Bold-lightweight.ttf') }}');
    }

    @page {
      size: A4 portrait;
      margin: 5mm 10mm;
    }

    body {
      font-family: GenShinGothic;
    }

    html {
      font-size: 16px;
    }

    body {
      margin: 0;
      padding: 0;
      word-wrap: break-word;
      background: #fff;
    }

    body * {
      margin: 0;
      padding: 0;
    }

    header {
      background: #3c8dbc;
    }

    header h1 {
      color: #fff;
      padding: 0.2rem 0.6rem 0.4rem 0.5rem;
      vertical-align: middle;
      font-size: 1.5rem;
    }

    .bl_contact {
      margin-top: 1.3rem;
      content: "";
      display: block;
      clear: both;
    }

    .bl_contact .bl_contact_customer {
      width: 60%;
      float: left;
      box-sizing: border-box;
    }

    .bl_contact .bl_contact_customer p {
      line-height: 1rem;
      font-size: 0.8rem;
    }

    .bl_contact .bl_contact_invoice {
      width: 40%;
      float: left;
      box-sizing: border-box;
    }

    .bl_contact .bl_contact_invoice .bl_contact_invoice_img {
      width: 130px;
      display: block;
      margin-bottom: 1.5rem;
    }

    .bl_contact .bl_contact_invoice .bl_contact_invoice_img img {
      width: 100%;
      display: block;
    }

    .bl_contact .bl_contact_invoice .bl_contact_invoice_text {
      display: inline-block;
      width: 70%;
    }

    .bl_contact .bl_contact_invoice .bl_contact_invoice_text p {
      line-height: 0.7rem;
      font-size: 0.7rem;
    }

    .bl_contact .bl_contact_invoice .bl_contact_invoice_stamp {
      display: inline-block;
      width: 50px;
    }

    .bl_contact .bl_contact_invoice .bl_contact_invoice_stamp img {
      width: 100%;
      display: block;
    }

    .bl_greet {
      content: "";
      display: block;
      clear: both;
      margin-bottom: 1rem;
      font-size: 0.8rem;
      line-height: 0.8rem;
    }

    .bl_invoicePrice {
      margin-bottom: 1.3rem;
    }

    .bl_invoicePrice .bl_invoicePrice_total {
      content: "";
      display: block;
      clear: both;
      border-bottom: 3px solid #000;
      padding-bottom: 3px;
    }

    .bl_invoicePrice .bl_invoicePrice_total dt {
      display: inline-block;
      width: 60%;
      font-weight: bold;
    }

    .bl_invoicePrice .bl_invoicePrice_total dd {
      display: inline-block;
      width: 38%;
    }

    .bl_invoicePrice .bl_invoicePrice_total dd span {
      font-size: 1.2rem;
      font-weight: bold;
      margin: 0 0.4rem;
    }

    .bl_invoicePrice .bl_invoicePrice_tax {
      border-bottom: 1px solid #000;
      padding: 6px 0;
    }

    .bl_invoicePrice .bl_invoicePrice_tax dt {
      display: inline-block;
      width: 60%;
      font-size: 0.8rem;
    }

    .bl_invoicePrice .bl_invoicePrice_tax dd {
      display: inline-block;
      width: 38%;
      font-size: 0.8rem;
    }

    .bl_invoicePrice .bl_invoicePrice_tax dd span {
      font-weight: bold;
      margin-right: 0.4rem;
    }

    .bl_invoiceDate {
      content: "";
      display: block;
      clear: both;
    }

    .bl_invoiceDate dl {
      border-bottom: 2px solid #000;
      float: left;
      width: 30%;
      margin-right: 3%;
      margin-bottom: 1.3rem;
    }

    .bl_invoiceDate dl dt {
      font-size: 0.9rem;
      font-weight: bold;
    }

    .bl_invoiceDate dl dd {
      font-size: 0.8rem;
    }

    .bl_addr {
      margin-bottom: 1.3rem;
      font-size: 0.8rem;
      line-height: 0.8rem;
      content: "";
      display: block;
      clear: both;
    }

    .bl_addr dl dt {
      font-weight: bold;
      font-size: 0.9rem;
    }

    .bl_addr dl dd {
      font-size: 0.9rem;
    }

    .bl_detail {
      margin-bottom: 1.3rem;
    }

    .bl_detail table {
      border-collapse: collapse;
      width: 100%;
    }

    .bl_detail table tbody tr:nth-of-type(2n-1) {
      background: rgba(60, 141, 188, 0.1);
    }

    .bl_detail table tbody tr:nth-child(1) {
      background: rgba(60, 141, 188, 0.3);
    }

    .bl_detail table tbody tr th {
      border: solid 1px;
      /* 枠線指定 */
      padding: 2px;
      font-size: 0.7rem;
    }

    .bl_detail table tbody tr th:nth-child(1) {
      width: 42%;
    }

    .bl_detail table tbody tr th:nth-child(2) {
      width: 13%;
    }

    .bl_detail table tbody tr th:nth-child(3) {
      width: 10%;
    }

    .bl_detail table tbody tr th:nth-child(4) {
      width: 13%;
    }

    .bl_detail table tbody tr th:nth-child(5) {
      width: 32%;
    }

    .bl_detail table tbody tr td {
      padding: 2px;
      font-size: 0.7rem;
      border: solid 1px;
    }

    .bl_detail table tfoot tr td {
      padding: 2px;
      border: solid 1px;
      /* 枠線指定 */
    }

    .bl_detail table tfoot tr td.el_title {
      background: rgba(77, 77, 77, 0.4);
    }

    .bl_message {
      width: 100%;
      word-wrap: break-word;
      margin-bottom: 1.3rem;
    }

    .el_font_size_s {
      font-size: 0.5rem;
    }

    .el_font_size_m {
      font-size: 0.8rem;
    }

    .el_font_size_l {
      font-size: 1rem;
    }

    .el_text_left {
      text-align: left;
    }

    .el_text_center {
      text-align: center;
    }

    .el_text_right {
      text-align: right;
    }
  </style>
</head>

<body>
  <header>
    <h1>請求書</h1>
  </header>
  <img src="" alt="">
  <div class="bl_contact">
    <div class="bl_contact_customer">
      <p class="">{{ $data->customer_post1 ?? '' }}-{{ $data->customer_post2 ?? '' }}</p>
      <p class>{{ $data->customer_addr1 ?? '都道府県' }} {{ $data->customer_addr2 ?? '市区町村' }}
        {{ $data->customer_addr3 ?? '丁目 番地 号' }}</p>
      {{ $data->customer_addr4 ?? '建物名 部屋番号' }}</p>
      <p>{{ $data->customer_company ?? '会社名' }}</p>
      <p>{{ $data->customer_office ?? '営業所' }}</p>
      <p>{{ $data->customer_name ?? '名前' }}</p>
    </div>
    <div class="bl_contact_invoice">
      <div class="bl_contact_invoice_img">
        @if (isset($_GET['type']) && $_GET['type'] === 'html_preview')
          <img src="{{ asset('images/common/logo.png') }}" alt="">
        @else
          <img src="{{ public_path('images/common/logo.png') }}" alt="">
        @endif
      </div>
      <div class="bl_contact_invoice_text">
        <p>{{ $data->invoice_post1 ?? '郵便番号' }}-{{ $data->invoice_post2 ?? '郵便番号' }}</p>
        <p>{{ $data->invoice_addr1 ?? '都道府県' }} {{ $data->invoice_addr2 ?? '市区町村' }}
          {{ $data->invoice_addr3 ?? '番地以降' }}
        </p>
        <p>{{ $data->invoice_company ?? '会社名' }}</p>
        <p>{{ $data->invoice_name ?? '名前 様' }}</p>
      </div>

      <div class="bl_contact_invoice_stamp">
        @if (isset($_GET['type']) && $_GET['type'] === 'html_preview')
          <img src="{{ asset('images/common/hanko.jpg') }}" alt="">
        @else
          <img src="{{ public_path('images/common/hanko.jpg') }}" alt="">
        @endif
      </div>
    </div>

  </div>

  <div class="bl_greet">
    平素は格別のお引き立てを賜り厚く御礼申し上げます。<br>
    下記の通りご請求申し上げます。よろしくお願い申し上げます。
  </div>

  <div class="bl_invoicePrice">

    <dl class="bl_invoicePrice_total">
      <dt>ご請求</dt>
      <dd>
        <span>{{ number_format($data->total_price_tax) ?? '' }}</span>
        円(税込)
      </dd>
    </dl>
    <dl class="bl_invoicePrice_tax">
      <dt>消費税額</dt>
      <dd><span>{{ number_format($data->total_tax) ?? '' }}</span>円 (消費税率10%)</dd>
    </dl>
  </div>

  <div class="bl_invoiceDate">
    <dl>
      <dt>ご使用期間</dt>
      <dd>{{ $data->from_date_period ?? '' }} ~ {{ $data->to_date_period ?? '' }}</dd>
    </dl>
    <dl>
      <dt>請求日</dt>
      <dd>{{ $data->date_billing ?? '' }}</dd>
    </dl>

    <dl>
      <dt>お振込期限</dt>
      <dd>{{ $data->date_deadline ?? '' }}</dd>
    </dl>


    </table>
  </div>

  <div class="bl_addr">
    <dl>
      <dt>振込先</dt>
      @if ($data->account_name && $data->bank && $data->bank_branch && $data->account_type && $data->account_num)
        <dd>名義：{{ $data->account_name ?? '' }}</dd>
        <dd>{{ $data->bank ?? '' }} {{ $data->bank_branch ?? '' }} {{ $data->account_type ?? '' }}
          口座番号:{{ $data->account_num ?? '' }}</dd>
      @else
        <dd>{{ $data->transfer }}</dd>
      @endif

    </dl>
  </div>

  <div class="bl_message el_font_size_m">
    {{ $data->message ?? '' }}
  </div>


  <div class="bl_detail">
    <table>
      <tbody>

        <tr>
          <th>品目(ドライバー名など)</th>
          <th>単価</th>
          <th>数量</th>
          <th>金額(税抜き)</th>
          <th>備考</th>
        </tr>
        @if ($task_list)
          @foreach ($task_list as $task)
            <tr>
              <td>{{ $task->joinDriver->full_name ?? '' }}</td>
              <td class="el_text_right">{{ number_format($data->unit_price) ?? '' }}</td>
              <td class="el_text_right">1</td>
              <td class="el_text_right">{{ number_format($data->unit_price) ?? '' }}</td>
              <td>ID:{{ $task->id }} 稼働日:{{ $task->taskDateYmd ?? '' }} {{ $task->joinTaskStatus->name ?? '' }}</td>
            </tr>
          @endforeach
        @endif

        @if ($data->product)
          @foreach ($product_list as $item)
            <tr>
              <td>{{ $item['name'] }}</td>
              <td class="el_text_right">{{ number_format($item['unit_price']) ?? '' }} </td>
              <td class="el_text_right">{{ $item['quantity'] ?? '' }}</td>
              <td class="el_text_right">{{ number_format($item['system_price']) ?? '' }} </td>
              <td>{{ $item['note'] ?? '' }}</td>
            </tr>
          @endforeach
        @endif

      </tbody>

    </table>
  </div>
</body>

</html>
