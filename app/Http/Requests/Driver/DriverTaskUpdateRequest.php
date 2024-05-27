<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\Driver\HasDriverTaskRule;
use Illuminate\Support\Facades\Route;
use App\Rules\Common\DriverTaskPlanAllowDriverRule;

use App\Models\DriverTask;

class DriverTaskUpdateRequest extends FormRequest
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
        $login_id = auth('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = auth()->id();
            $login_user = auth()->user();
        }

        $type = $this->type ?? '';
        $type_list = ['reject', 'accept'];

        $task_id = $this->task_id ?? '';
        $task_select = DriverTask::select()->where([
            ['id', '=', $task_id],
        ])->first();

        $driver_task_plan_id = $task_select->driver_task_plan_id;
        $driver_id = $login_id;

        // 指名なしの場合は、HasDriverTaskRuleを適用しない
        if ($task_select->driver_id) {
            $result = [
                'task_id' => [new HasDriverTaskRule(), new DriverTaskPlanAllowDriverRule($driver_task_plan_id, $driver_id)],
                'type' => ["required", Rule::in($type_list)],
            ];
        } else {
            $result = [
                'task_id' => [new DriverTaskPlanAllowDriverRule($driver_task_plan_id, $driver_id)],
                'type' => ["required", Rule::in($type_list)],
            ];
        }

        return $result;
    }

    /** 
     * パスパラメータのidをバリデーションに含める 
     */
    public function validationData()
    {
        return array_merge($this->request->all(), [
            'task_id' => $this->route('task_id'),
        ]);
    }

    /**
     * 独自バリデーションのエラーメッセージを生成する
     * APIの場合は、Unicode文字列をエスケープしないようにする。
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        if (Route::is('api.*')) {
            throw new HttpResponseException(
                response()->json([
                    'message' => '入力内容にエラーがあります。',
                    'errors' => $validator->errors(),
                ], 422, [], JSON_UNESCAPED_UNICODE)
            );
        } else {
            parent::failedValidation($validator);
        }
    }
}
