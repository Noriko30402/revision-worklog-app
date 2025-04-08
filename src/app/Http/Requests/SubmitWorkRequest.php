<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use Illuminate\Validation\Validator;


class SubmitWorkRequest extends FormRequest
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
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'date' => 'required|date',
            'remarks' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'clock_in.required' => '出勤時間は必須です。',
            'clock_out.required' => '退勤時間は必須です。',
            'clock_in.date_format' => '出勤時間もしくは退勤時間が不適切な値です。',
            'clock_out.date_format' => '出勤時間もしくは退勤時間が不適切な値です。',
            'break_start.date_format' => '休憩開始時間の形式が正しくありません。',
            'break_end.date_format' => '休憩終了時間の形式が正しくありません。',
            'date.required' => '日付は必須です。',
            'remarks.required' => '備考を記入してください。',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {
            try {
                $date = $this->date;

                $clockIn = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $this->clock_in);
                $clockOut = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $this->clock_out);
                $breakStart = $this->break_start
                    ? Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $this->break_start)
                    : null;
                $breakEnd = $this->break_end
                    ? Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $this->break_end)
                    : null;

                // 1. 出退勤の整合性チェック
                if ($clockIn->gte($clockOut)) {
                    $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です。');
                }

                // 2. 休憩が勤務時間外に設定されているかどうか
                if ($breakStart && ($breakStart->lt($clockIn) || $breakStart->gt($clockOut))) {
                    $validator->errors()->add('break_start', '休憩時間が勤務時間外です。');
                }

                if ($breakEnd && ($breakEnd->lt($clockIn) || $breakEnd->gt($clockOut))) {
                    $validator->errors()->add('break_end', '休憩時間が勤務時間外です。');
                }

            } catch (\Exception $e) {
                // フォーマットエラー等は rules() の date_format バリデーションで対応
            }
        });
    }
}
