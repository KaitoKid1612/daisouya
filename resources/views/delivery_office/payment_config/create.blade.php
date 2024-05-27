@extends('layouts.delivery_office.app')

@section('title')
  支払い方法 クレジットカード登録
@endsection

@section('content')
  <div class="bl_paymentCreate">
    <div class="bl_paymentCreate_inner">
      <div class="bl_paymentCreate_inner_head">
        <div class="bl_paymentCreate_inner_head_ttl">
          <h2>クレジットカード登録</h2>
        </div>
      </div>

      <div class="bl_paymentCreate_inner_content">

        <section class="c_credit">
          <div class="c_credit_item c_credit_name">
            <input id="card-holder-name" type="text" placeholder="カード名義人">
          </div>

          {{-- Stripeのフォーム要素が埋め込まれる --}}
          {{-- カード番号 --}}
          <div id="card-number-element" class="c_credit_item c_credit_number"></div>

          <div class="c_credit_flex">
            {{-- カード期限 --}}
            <div id="card-expiry-element" class="c_credit_item c_credit_expiry"></div>

            {{-- カードCVC --}}
            <div id="card-cvc-element" class="c_credit_item c_credit_cvc"></div>
          </div>

          <div class="c_credit_submit">
            <button id="card-button" data-secret="{{ $intent->client_secret }}">
              登録する
            </button>
          </div>

          <form action="{{ route('delivery_office.payment_config.store') }}" method="POST" id="updateForm"class="js_confirm">
            @csrf
            <input type="hidden" name="payment_method">
          </form>
        </section>

      </div>
    </div>
  </div>
  </div>
@endsection

@section('script_bottom')
  {{-- StripeのJS SDKの読み込み --}}
  <script src="https://js.stripe.com/v3/"></script>

  <script>
    /**
     * Stripe カード登録処理
     **/
    // document.addEventListener('DOMContentLoaded', function() {
    const stripe = Stripe('{{ env('STRIPE_KEY') }}');
    const elements = stripe.elements();


    let stripe_style = {
      base: {
        color: '#000',
        fontSize: '1.1rem',
        // fontSmoothing: 'antialiased',
        ':-webkit-autofill': {
          color: '#fce883',
        },
        '::placeholder': {
          color: '#9C9C9C',
        },
      },
      invalid: {
        iconColor: '#FFC7EE',
        color: 'RED',
      },
    };

    /* フォーム要素作成 */
    const cardNumberElement = elements.create('cardNumber', {
      placeholder: 'カード番号',
      style: stripe_style,
    });
    const cardExpiryElement = elements.create('cardExpiry', {
      style: stripe_style,
    });
    const cardCvcElement = elements.create('cardCvc', {
      placeholder: 'CVC',
      style: stripe_style,
    });

    /* フォーム要素マウント */
    // cardElement.mount('#card-element');
    cardNumberElement.mount('#card-number-element');
    cardExpiryElement.mount('#card-expiry-element');
    cardCvcElement.mount('#card-cvc-element');

    const cardHolderName = document.getElementById('card-holder-name'); // カード名義人
    const cardButton = document.getElementById('card-button'); // 登録ボタン
    const clientSecret = cardButton.dataset.secret;

    cardButton.addEventListener('click', async (e) => {
      // カード情報の登録 (Stripeとの通信)
      const {
        setupIntent,
        error
      } = await stripe.confirmCardSetup(
        clientSecret, {
          payment_method: {
            card: cardNumberElement, // カード番号を渡せば期限もCVCも含まれるぽい
            billing_details: {
              name: cardHolderName.value // カード名義人
            }
          }
        }
      );

      if (error) {
        alert(error.message);
      } else {
        // クレジットカードの登録に成功したので、Laravel側にトークンをPostする
        const updateForm = document.getElementById('updateForm');
        updateForm.payment_method.value = setupIntent.payment_method;
        updateForm.submit();
      }
    });
    // });
  </script>
  <script src="{{ asset('./js/libs/Functions/form_parts.js') }}"></script>
@endsection
