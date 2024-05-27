<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Common\KatakanaRule;

class WebConfigBaseUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'site_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_name_kana' => ['required', 'string', 'max: 255', new KatakanaRule()],
            'post_code1' => 'required|numeric|digits:3',
            'post_code2' => 'required|numeric|digits:4',
            'addr1_id' => 'required|integer|exists:prefectures,id',
            'addr2' => 'required|string|max:255',
            'addr3' => 'required|string|max:255',
            'addr4' => 'nullable|string|max:255',
            'tel' => 'numeric|digits_between:10,11',
            'commerce_law_driver' => 'required|string',
            'terms_service_delivery_office' => 'required|string',
            'terms_service_driver' => 'required|string',
            'privacy_policy_driver' => 'required|string',
            'user_guide_path_delivery_office' => 'sometimes|mimes:pdf|max:20480',
            'user_guide_path_driver' => 'sometimes|mimes:pdf|max:20480',
            'transfer' => 'required|string',
        ];
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'site_name' => 'サイト名',
            'company_name' => '会社名',
            'company_name_kana' => '会社名(カナ)',
            'post_code1' => '郵便番号1',
            'post_code2' => '郵便番号2',
            'addr1_id' => '住所(都道府県)',
            'addr2' => '住所(市区町村)',
            'addr3' => '住所(丁目 番地 号)',
            'addr4' => '住所(建物名 部屋番号)',
            'tel' => '電話番号',
            'commerce_law_driver' => '特定商取引法に基づく表記',
            'terms_service_delivery_office' => 'ご利用規約 依頼者',
            'terms_service_delivery_driver' => 'ご利用規約 ドライバー',
            'user_guide_path_delivery_office' => 'ユーザーガイド 依頼者',
            'user_guide_path_driver' => 'ユーザーガイド ドライバー',
            'privacy_policy_driver' => 'プライバシーポリシー',
            'transfer' => '振込情報',
        ];
    }
}
