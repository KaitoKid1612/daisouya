@extends('layouts.admin.app')

@section('title')
  PDF請求書 作成
@endsection

@section('content')
  {{ session('msg') ?? '' }}

  <div class="bl_pdfInvoiceCreate">
    <div class="bl_pdfInvoiceCreate_inner">
      <div class="bl_pdfInvoiceCreate_inner_head">
        <div class="bl_pdfInvoiceCreate_inner_head_ttl">
          <h2>PDF請求書 作成<span>/ create PDF invoice</span></h2>
        </div>
      </div>

      <div class="bl_pdfInvoiceCreate_inner_content">
        <form>
          @csrf
          <section class="bl_pdfInvoiceCreate_inner_content_form">

            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="from_task_date">稼働日 範囲</label>
              <input type="date" name='from_task_date' id="from_task_date"
                value="{{ old('from_task_date', $_GET['from_task_date'] ?? '') }}" id='from_task_date'
                class='el_width10rem'>

              <span>-</span>

              <input type="date" name='to_task_date' id="to_task_date"
                value="{{ old('to_task_date', $_GET['to_task_date'] ?? '') }}" id='to_task_date'
                class='el_width10rem'>
            </div>
            <div class="bl_pdfInvoiceCreate_inner_content_form_list">
              <div class="bl_pdfInvoiceCreate_inner_content_form_list_box">
                <h4 class='bl_pdfInvoiceCreate_inner_content_form_list_box_ttl'>
                  稼働ステータス
                </h4>
                <ul>
                  @foreach ($task_status_list as $task_status)
                    <li class='c_form_checkbox'>
                      <input type="checkbox"
                        name='task_status_id[]'
                        value="{{ $task_status['id'] }}"
                        id='task_status_id{{ $task_status['id'] }}' {{ $task_status['id'] == 4 ? 'checked' : '' }}>
                      <label
                        for="task_status_id{{ $task_status['id'] }}">{{ $task_status['name'] }}
                      </label>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>

            <div class="bl_pdfInvoiceCreate_inner_content_form_caption">
              <h3>
                営業所選択
              </h3>
              <aside>※特定の営業所で絞り込みたい場合は選択してください。</aside>
            </div>
            <div class="bl_pdfInvoiceCreate_inner_content_form_list">
              @foreach ($delivery_multi_list as $delivery_list)
                <div class="bl_pdfInvoiceCreate_inner_content_form_list_box">
                  <h4 class='bl_pdfInvoiceCreate_inner_content_form_list_box_ttl'>
                    {{ $delivery_list['company']['name'] }}
                  </h4>
                  <ul>
                    @foreach ($delivery_list['office_list'] as $office)
                      <li class='c_form_checkbox'>
                        <input type="checkbox"
                          name='delivery_office_id[]'
                          value='{{ $office['id'] }}'
                          id='{{ $delivery_list['company']['name'] }}_{{ $office['name'] }}'>
                        <label
                          for="{{ $delivery_list['company']['name'] }}_{{ $office['name'] }}">{{ $office['name'] }}
                        </label>
                      </li>
                    @endforeach
                  </ul>
                </div>
              @endforeach
            </div>

            <div class="bl_pdfInvoiceCreate_inner_content_form_caption">
              <h3>
                請求元
              </h3>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="invoice_post1">郵便番号</label>
              <input type="text" name="invoice_post1" id="invoice_post1" value="{{ old('', $config_base->post_code1 ?? '') }}"
                class='el_width10rem'>
              <span>-</span>
              <input type="text" name="invoice_post2" value="{{  old('invoice_post2', $config_base->post_code2 ?? '' ) }}"
                class='el_width10rem'>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="invoice_addr1">都道府県</label>
              <div class="c_form_select">
                <select name="invoice_addr1" id="invoice_addr1">
                  <option disabled selected>
                    選択してください。
                  </option>
                  @foreach ($prefecture_list as $prefecture)
                    <option value="{{ $prefecture->id }}"
                      {{ old('invoice_addr1', $config_base->addr1_id ?? '') == $prefecture->id ? 'selected' : '' }}>
                      {{ $prefecture->name ?? '' }}
                    </option>
                  @endforeach
                </select>
              </div>
              <p class="el_error_msg">
                @error('addr1_id')
                  {{ $message }}
                @enderror
              </p>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="invoice_addr2">市区町村</label>
              <input type="text" name="invoice_addr2" id="invoice_addr2" value="{{ old('invoice_addr2', $config_base->addr2 ?? '') }}">
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="invoice_addr3">丁目 番地 号</label>
              <input type="text" name="invoice_addr3" id="invoice_addr3" value="{{ old('invoice_addr3', $config_base->addr3 ?? '') }}">
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="invoice_addr4">建物名 部屋番号</label>
              <input type="text" name="invoice_addr4" id="invoice_addr4" value="{{ old('invoice_addr4', $config_base->addr4 ?? '') }}">
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="invoice_company">会社名</label>
              <input type="text" name="invoice_company" value="{{ old('invoice_company', $config_base->company_name ?? '') }}">
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="invoice_name">氏名</label>
              <input type="text" name="invoice_name" id="invoice_name" value="{{ old('invoice_name', $config_base->owner_name ?? '') }}">
            </div>

            <div class="bl_pdfInvoiceCreate_inner_content_form_caption">
              <h3>
                請求先
              </h3>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="customer_company">会社名</label>
              <input type="text" name="customer_company" id="customer_company" value="{{ old('customer_company') }}">
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="customer_office">営業所</label>
              <input type="text" name="customer_office" id="customer_office" value="{{ old('customer_office') }}">
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="customer_name">氏名</label>
              <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}">
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="customer_post1">郵便番号</label>
              <input type="text" name="customer_post1" id="customer_post1" value="{{ old('customer_post1') }}" class='el_width10rem'>
              <span>-</span>
              <input type="text" name="customer_post2" id="customer_post2" value="{{ old('customer_post2') }}" class='el_width10rem'>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="customer_addr1">都道府県</label>
              <div class="c_form_select">
                <select name="customer_addr1" id="customer_addr1">
                  <option disabled selected>
                    選択してください。
                  </option>
                  @foreach ($prefecture_list as $prefecture)
                    <option
                      value="{{ $prefecture->id }}" {{ old('customer_addr1') == $prefecture->id ? 'selected' : '' }}>
                      {{ $prefecture->name ?? '' }}
                    </option>
                  @endforeach
                </select>
              </div>
              <p class="el_error_msg">
                @error('customer_addr1')
                  {{ $message }}
                @enderror
              </p>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="customer_addr2">市区町村</label>
              <input type="text" name="customer_addr2" id="customer_addr2" value="">
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="customer_addr3">丁目 番地 号</label>
              <input type="text" name="customer_addr3" id="customer_addr3" value="">
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="customer_addr4">建物名 部屋番号</label>
              <input type="text" name="customer_addr4" id="customer_addr4" value="">
            </div>

            <div class="bl_pdfInvoiceCreate_inner_content_form_caption">
              <h3>
                日程
              </h3>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="from_date_period">ご使用期間</label>
              <input type="date" name="from_date_period" id="from_date_period" value=""
                class='el_width10rem'>
              <span>-</span>
              <input type="date" name="to_date_period" value="" class='el_width10rem'>
            </div>

            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="date_billing">請求日</label>
              <input type="date" name="date_billing" id="date_billing" value="">
            </div>

            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="date_deadline">振込期限</label>
              <input type="date" name="date_deadline" id="date_billing" value="">
            </div>

            <div class="bl_pdfInvoiceCreate_inner_content_form_caption">
              <h3>
                振込情報
              </h3>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="transfer">振込</label>
              <textarea name="transfer" id="transfer">{{ $config_base->transfer ?? '' }}</textarea>
            </div>

            <div class="bl_pdfInvoiceCreate_inner_content_form_caption">
              <h3>
                金額
              </h3>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="unit_price">単価</label>
              <input type="number" name="unit_price" id="unit_price" value="" required>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="total_price">請求額</label>
              <input type="text" name="total_price" id="total_price" value="">
              <aside>※未入力の場合は自動計算</aside>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="tax">消費税額</label>
              <input type="text" name="tax" id="tax" value="">
              <aside>※未入力の場合は自動計算</aside>
            </div>


            <div class="bl_pdfInvoiceCreate_inner_content_form_caption">
              <h3>
                明細に追加
              </h3>
            </div>

            <ul class="bl_pdfInvoiceCreate_inner_content_form_addList js_ul">
            </ul>
            <div class="c_button_box">
              <button class="js_add_form_item_btn">追加ボタン</button>
            </div>


            <div class="bl_pdfInvoiceCreate_inner_content_form_caption">
              <h3>
                文章
              </h3>
            </div>
            <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_item">
              <label for="message">メッセージ</label>
              <textarea name="message" id="message"></textarea>
            </div>

            <div class="c_form_submit bl_pdfInvoiceCreate_inner_content_form_submit">
              <input type="submit" value="HTMLプレビュー" formmethod="POST"
                formtarget="_blank"formaction="{{ route('admin.pdf_invoice.store', [
                    'type' => 'html_preview',
                ]) }}">
            </div>

            <div class="c_form_submit bl_pdfInvoiceCreate_inner_content_form_submit">
              <input type="submit" value="PDFプレビュー" formmethod="POST"
                formtarget="_blank"formaction="{{ route('admin.pdf_invoice.store', [
                    'type' => 'pdf_preview',
                ]) }}">
            </div>

            <div class="c_form_submit bl_pdfInvoiceCreate_inner_content_form_submit">
              <input type="submit" value="PDFダウンロード" formmethod="POST"
                formtarget="_blank"formaction="{{ route('admin.pdf_invoice.store', [
                    'type' => 'pdf',
                ]) }}">
            </div>

          </section>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script>
    /** 
     * フォーム要素  手動追加処理
     */

    let $add_btn = document.querySelector('.js_add_form_item_btn'); // 追加ボタン
    let $ul = document.querySelector('.js_ul'); // 追加される要素の親要素
    let count = 0; // 作成したフォームの数をカウント、多重配列のkeyになる。

    $add_btn.addEventListener('click', (e) => {
      e.preventDefault();
      console.log($ul.innerHTML);
      $add_html = `
<li id='js_li_${count}'>
  <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_addList_item">
    <label for="name_${count}">品目(ドライバー名など)</label>
    <input type="text" name="product[${count}][name]" value="" id="name_${count}">
  </div>
  <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_addList_item">
    <label for="unit_price_${count}">単価</label>
    <input type="number" name="product[${count}][unit_price]"
      value="" id="unit_price_${count}">
  </div>
  <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_addList_item">
    <label for="quantity_${count}">数量</label>
    <input type="number" name="product[${count}][quantity]" value="1" id="quantity_${count}">
  </div>
  <div class="c_form_item bl_pdfInvoiceCreate_inner_content_form_addList_item">
    <label for="note_${count}">備考</label>
    <input type="text" name="product[${count}][note]" value="" id="note_${count}">
  </div>
  <div class="c_button_box">
  <button class="js_del_form_item_btn " data-count='${count}'>削除</button>
  </div>
</li>
`;

      $ul.insertAdjacentHTML('beforeend', $add_html);

      // 表示アニメーション
      let js_li = document.getElementById(`js_li_${count}`);
      setTimeout(() => {
        js_li.classList.add('js_show')
      });
      count++;


      /* 削除処理 */
      let $del_btn_list = document.querySelectorAll('.js_del_form_item_btn'); // 削除ボタンリスト
      $del_btn_list.forEach(($del_btn) => {
        $del_btn.addEventListener('click', (e) => {
          e.preventDefault();
          console.log($del_btn);
          let del_count = $del_btn.dataset.count; // 削除する要素の番号
          let $del_li = document.getElementById(`js_li_${del_count}`); // 要素取得
          $del_li.classList.remove('js_show'); // 非表示アニメーション
          setTimeout(() => {
            $del_li.remove(); // 要素削除
          }, 200);
        });
      });

    });
  </script>
@endsection
