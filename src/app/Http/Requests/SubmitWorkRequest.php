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
            'clock_in' => ['required'],
            'clock_out' => ['required'],
            'rest_in' => ['nullable', 'array'],
            'rest_in.*' => ['nullable'],
            'rest_out' => ['nullable', 'array'],
            'rest_out.*' => ['nullable'],

            'comment' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'comment.required' => '備考を記入してください。',

        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $clock_in = $this->input('clock_in');
            $clock_out = $this->input('clock_out');
            $rest_ins = $this->input('rest_in', []);
            $rest_outs = $this->input('rest_out', []);

        if ($clock_in && $clock_out) {
            $clockIn = \Carbon\Carbon::createFromFormat('H:i', $clock_in);
            $clockOut = \Carbon\Carbon::createFromFormat('H:i', $clock_out);

        if ($clockIn >= $clockOut) {
            $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }

        $hasRestError = false;

        foreach ($rest_ins as $index => $rest_in) {
            if (!empty($rest_in)) {
                $restIn = \Carbon\Carbon::createFromFormat('H:i', $rest_in);
                    if ($restIn < $clockIn || $restIn > $clockOut) {
                        if (!$hasRestError) {
                            $validator->errors()->add("rest_in.$index", '休憩開始時間が勤務時間外です');
                            $hasRestError = true;
                        }
                    }
                }
            }

        foreach ($rest_outs as $index => $rest_out) {
            if (!empty($rest_out)) {
                $restOut = \Carbon\Carbon::createFromFormat('H:i', $rest_out);
                    if ($restOut < $clockIn || $restOut > $clockOut) {
                        if (!$hasRestError) {
                            $validator->errors()->add("rest_out.$index", '休憩終了時間が勤務時間外です');
                            $hasRestError = true;
                        }
                    }
                }
            }
            }
        });
    }
}
