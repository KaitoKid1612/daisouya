@extends('layouts.admin.app')

@section('title')
  基本設定 編集
@endsection

@section('script_head')
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  <div class="bl_edit">
    <div class="bl_edit_inner">
      <div class="bl_edit_inner_head">
        <div class="bl_edit_inner_head_ttl">
          <h2>基本設定 編集</h2>
        </div>
      </div>

      <div class="bl_edit_inner_content">
        <section class="bl_edit_inner_content_data">
          <form action="{{ route('admin.web_config_base.update') }}" method="POST" enctype="multipart/form-data" class="js_quill js_confirm">
            @csrf
            <div class="bl_edit_inner_content_data_form">

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="site_name">サイト名</label>
                <input type="text" name="site_name" value="{{ old('site_name', $config_base->site_name ?? '') }}" id="site_name">
                <p class="el_error_msg">
                  @error('site_name')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="company_name">会社名</label>
                <input type="text" name="company_name" value="{{ old('company_name', $config_base->company_name ?? '') }}"
                  id="company_name">
                <p class="el_error_msg">
                  @error('company_name')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="company_name_kana">会社名(カナ)</label>
                <input type="text" name="company_name_kana" value="{{ old('company_name_kana', $config_base->company_name_kana ?? '') }}"
                  id="company_name_kana">
                <p class="el_error_msg">
                  @error('company_name_kana')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="post_code1">郵便番号</label>
                <input type="text" name="post_code1" value="{{ old('post_code1', $config_base->post_code1 ?? '') }}" id="post_code1"
                  class="el_width12rem">

                <span>-</span>

                <input type="text" name="post_code2" value="{{ old('post_code2', $config_base->post_code2 ?? '') }}" id="post_code2"
                  class="el_width12rem">
                <p class="el_error_msg">
                  @error('post_code1')
                    {{ $message }}
                  @enderror
                  @error('post_code2')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="addr1_id">都道府県</label>
                <div class="c_form_select">
                  <select name="addr1_id" id="addr1_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($prefecture_list as $prefecture)
                      <option value="{{ $prefecture->id }}" {{ isset($config_base->addr1_id) && old('addr1_id', $config_base->addr1_id) == $prefecture->id ? 'selected' : '' }}>
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


              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="addr2">市区町村</label>
                <input type="text" name="addr2" value="{{ old('addr2', $config_base->addr2 ?? '') }}" id="addr2">
                <p class="el_error_msg">
                  @error('addr2')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="addr3">丁目 番地 号</label>
                <input type="text" name="addr3" value="{{ old('addr3', $config_base->addr3 ?? '') }}" id="addr3">
                <p class="el_error_msg">
                  @error('addr3')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="addr4">建物名 部屋番号</label>
                <input type="text" name="addr4" value="{{ old('addr4', $config_base->addr4 ?? '') }}" id="addr4">
                <p class="el_error_msg">
                  @error('addr4')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="tel">電話番号</label>
                <input type="text" name="tel" value="{{ old('tel', $config_base->tel ?? '') }}" id="tel">
                <p class="el_error_msg">
                  @error('tel')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_editor
              bl_edit_inner_content_data_form_item">
                <label for="commerce_law_delivery_office">特定商取引法に基づく表記 依頼者</label>

                <textarea name="commerce_law_delivery_office" id="commerce_law_delivery_office" class='quill_textarea el_hidden'>{{ old('commerce_law_delivery_office', $config_base->commerce_law_delivery_office ?? '') }}</textarea>

                <div id="editor_commerce_law_delivery_office" class="quill_editor">
                  {!! old('commerce_law_delivery_office', $config_base->commerce_law_delivery_office ?? '') !!}
                </div>

                <p class="el_error_msg">
                  @error('terms_service_delivery_office')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_editor
              bl_edit_inner_content_data_form_item">
                <label for="commerce_law_driver">特定商取引法に基づく表記 ドライバー</label>
                <textarea name="commerce_law_driver" id="commerce_law_driver" class='quill_textarea el_hidden'>{{ old('commerce_law_driver', $config_base->commerce_law_driver ?? '') }}</textarea>

                <div id="editor_commerce_law_driver" class="quill_editor">
                  {!! old('commerce_law_driver', $config_base->commerce_law_driver ?? '') !!}
                </div>

                <p class="el_error_msg">
                  @error('terms_service_delivery_office')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_editor
               bl_edit_inner_content_data_form_item">
                <label for="terms_service_delivery_office">ご利用規約 依頼者</label>

                <textarea name="terms_service_delivery_office" id="terms_service_delivery_office" class='quill_textarea el_hidden'>{{ old('terms_service_delivery_office', $config_base->terms_service_delivery_office ?? '') }}</textarea>

                <div id="editor_terms_service_delivery_office" class="quill_editor">
                  {!! old('terms_service_delivery_office', $config_base->terms_service_delivery_office ?? '') !!}
                </div>

                <p class="el_error_msg">
                  @error('terms_service_delivery_office')
                    {{ $message }}
                  @enderror
                </p>
              </div>



              <div class="c_form_editor bl_edit_inner_content_data_form_item">
                <label for="terms_service_driver">ご利用規約 ドライバー</label>
                <textarea name="terms_service_driver" id="terms_service_driver" class='quill_textarea el_hidden'>{!! old('terms_service_driver', $config_base->terms_service_driver ?? '') !!}</textarea>

                <div id="editor_terms_service_driver" class="quill_editor">
                  {!! old('terms_service_driver', $config_base->terms_service_driver ?? '') !!}
                </div>
                <p class="el_error_msg">
                  @error('terms_service_driver')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_editor bl_edit_inner_content_data_form_item">
                <label for="privacy_policy_delivery_office">プライバシーポリシー 依頼者</label>
                <textarea name="privacy_policy_delivery_office" id="privacy_policy_delivery_office" class='quill_textarea el_hidden'>{!! old('privacy_policy_delivery_office', $config_base->privacy_policy_delivery_office ?? '') !!}</textarea>

                <div id="editor_privacy_policy_delivery_office" class="quill_editor">
                  {!! old('privacy_policy_delivery_office', $config_base->privacy_policy_delivery_office ?? '') !!}
                </div>
                <p class="el_error_msg">
                  @error('terms_service_driver')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_editor bl_edit_inner_content_data_form_item">
                <label for="privacy_policy_driver">プライバシーポリシー ドライバー</label>
                <textarea name="privacy_policy_driver" id="privacy_policy_driver" class='quill_textarea el_hidden'>{!! old('privacy_policy_driver', $config_base->privacy_policy_driver ?? '') !!}</textarea>

                <div id="editor_privacy_policy_driver" class="quill_editor">
                  {!! old('privacy_policy_driver', $config_base->privacy_policy_driver ?? '') !!}
                </div>
                <p class="el_error_msg">
                  @error('terms_service_driver')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="user_guide_path_delivery_office">ご利用ガイド 依頼者</label>
                <input type="file" accept=".pdf" name="user_guide_path_delivery_office"
                  id="user_guide_path_delivery_office">
                <p class="el_error_msg">
                  @error('user_guide_path_delivery_office')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="user_guide_path_driver">ご利用ガイド ドライバー</label>
                <input type="file" accept=".pdf" name="user_guide_path_driver"
                  id="user_guide_path_driver">
                <p class="el_error_msg">
                  @error('user_guide_path_driver')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="transfer">振込情報</label>
                <textarea type="text" name="transfer" id="transfer">{{ old('transfer', $config_base->transfer ?? '') }}</textarea>
                <p class="el_error_msg">
                  @error('transfer')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item el_submit">
                <input type="submit" value="編集" class='c_btn'>
              </div>
            </div>

          </form>
        </section>
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script>
    /* Quilエディタのオプション */
    var toolbarOptions = [
      ['bold', 'italic', 'underline', 'strike'],
      ['blockquote', 'code-block'],
      [{
        'header': 1
      }, {
        'header': 2
      }],
      [{
        'list': 'ordered'
      }, {
        'list': 'bullet'
      }],
      [{
        'header': [1, 2, 3, 4, 5, 6, false]
      }],
      [{
        'color': []
      }, {
        'background': []
      }], // dropdown with defaults from theme
    ];

    var options = {
      theme: 'snow',
      modules: {
        toolbar: toolbarOptions
      }
    };

    /* Quillエディタ生成 */
    // 特定商取引法に基づく表記 依頼者
    let quill_commerce_law_delivery_office = new Quill('#editor_commerce_law_delivery_office', options);

    // 特定商取引法に基づく表記 ドライバー
    let quill_commerce_law_driver = new Quill('#editor_commerce_law_driver', options);

    // ご利用規約 依頼者
    let quill_terms_service_delivery_office = new Quill('#editor_terms_service_delivery_office', options);

    // ご利用規約 ドライバー
    let quill_terms_service_driver = new Quill('#editor_terms_service_driver', options);

    // プライバシポリシー 依頼者
    let quill_privacy_policy_delivery_office = new Quill('#editor_privacy_policy_delivery_office', options);

    // プライバシポリシー ドライバー
    let quill_privacy_policy_driver = new Quill('#editor_privacy_policy_driver', options);

    /**
     * エディタでフォーム送信する
     * エディタの中身をテキストエリアに代入している。
     */
    let $form = document.querySelector('form.js_quill'); // form
    let quill_editor_list = document.querySelectorAll('.quill_editor'); // quillエディタ一覧
    let quill_textarea_list = document.querySelectorAll('.quill_textarea'); // quillテキストエリア一覧

    $form.addEventListener('submit', (e) => {
      e.preventDefault();

      let count = 0;
      quill_editor_list.forEach((editor_item) => {
        // quillのhtmlデータをテキストエリアに代入
        quill_textarea_list[count].value = editor_item.querySelector('.ql-editor').innerHTML;
        count++;
      });
    });
  </script>

  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
