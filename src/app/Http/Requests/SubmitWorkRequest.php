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
            'comment' => 'required',
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
            if ($this->clock_in >= $this->clock_out) {
                $validator->errors()->add('clock_in', '出勤時間は退勤時間より前でなければなりません。');
            }
        });
    }

    // public function withValidator($validator)
    // {
    //     $date = Carbon::parse($this->input('date'))->format('Y-m-d');
    //     $clockIn = Carbon::parse($date . ' ' . $this->input('clock_in'));
    //     $clockOut = Carbon::parse($date . ' ' . $this->input('clock_out'));
    //     $restStartRaw = Carbon::parse($date . ' ' . $this->input('rest_in'));
    //     $restEndRaw = Carbon::parse($date . ' ' . $this->input('rest_out'));

    //     $restStart = $restStartRaw ? Carbon::parse($date . ' ' . (is_array($restStartRaw) ? reset($restStartRaw) : $restStartRaw)) : null;
    //     $restEnd = $restEndRaw ? Carbon::parse($date . ' ' . (is_array($restEndRaw) ? reset($restEndRaw) : $restEndRaw)) : null;


    //     $validator->after(function ($validator) use($date, $clockIn,$clockOut,$restStart,$restEnd) {

    //         if($clockIn >$clockOut ){
    //             $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です。');
    //         }

    //         if ($restStart && ($restStart->lt($clockIn) || $restStart->gt($clockOut))) {
    //             $validator->errors()->add('rest_in', '休憩時間が勤務時間外です。');
    //         }

    //         if ($restEnd && ($restEnd->lt($clockIn) || $restEnd->gt($clockOut))) {
    //             $validator->errors()->add('rest_out', '休憩時間が勤務時間外です。');
    //         }
    //         dd($this->all());
    //     });
    // }
}