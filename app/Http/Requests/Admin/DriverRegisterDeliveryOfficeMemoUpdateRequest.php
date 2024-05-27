<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DriverRegisterDeliveryOfficeMemoUpdateRequest extends FormRequest
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
            'driver_id' => 'required|nullable|exists:drivers,id',
            "delivery_company_id" => 'nullable|exists:delivery_companies,id',
            'delivery_office_name' => 'required|string|max: 255',
            'post_code1' => 'required|numeric|digits:3',
            'post_code2' => 'required|numeric|digits:4',
            'addr1_id' => 'required|exists:prefectures,id',
            'addr2' => 'required|string|max: 255',
            'addr3' => 'required|string|max: 255',
            'addr4' => 'nullable|string|max: 255',
        ];
    }

    /**
     * name を変更(デフォルトname属性値)
     *
     */
    public function attributes()
    {
        return [
            'driver_id' => 'ドライバー',
            "delivery_company_id" => '運送会社',
            'delivery_office_name' => '営業所名',
            'post_code1' => '郵便番号1',
            'post_code2' => '郵便番号2',
            'addr1_id' => '住所(都道府県)',
            'addr2' => '住所(市区町村)',
            'addr3' => '住所(丁目 番地 号)',
            'addr4' => '住所(建物名 部屋番号)',
        ];
    }
}
