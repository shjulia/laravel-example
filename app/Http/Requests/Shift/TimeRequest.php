<?php

namespace App\Http\Requests\Shift;

use App\Http\Requests\FormRequest;

/**
 * Class TimeRequest
 * @package App\Http\Requests\Shift
 */
class TimeRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $isAdmin = $this->user()->isSuperAdmin();
        return [
            'time_from' => 'required|date_format:"H:i"',
            'time_to' => 'required|date_format:"H:i"',
            'start_date' => 'required|date' . (!$isAdmin ? '|after_or_equal:today' : ''),
            'end_date' => 'required|date|after_or_equal:start_date',
            'lunch_break' => 'integer'
        ];
    }

    /**
     * @param $validator
     */
    public function after($validator)
    {
        if (!empty($validator->failed())) {
            return;
        }
        $time1 = explode(':', $this->time_from);
        $time1 = (int)($time1[0]) * 60 + (int)($time1[1]);
        $time2 = explode(':', $this->time_to);
        $time2 = (int)($time2[0]) * 60 + (int)($time2[1]);
        $days = (strtotime($this->end_date) - strtotime($this->start_date)) / (60 * 60 * 24);
        if ($time1 > $time2) {
            $time2 += (24 * 60);
            //$validator->errors()->add('time_to', 'Time to field must be greater then time from field is required');
        } else {
            $days++; //because we must add first day
        }
        $lunch = $this->lunch_break * $days;
        $shiftTime = ($time2  - $time1) * $days - $lunch;
        if ($shiftTime < 120) {
            $validator->errors()->add('time_to', 'Minimum shift is 2 hours');
        } elseif (($time2 - $time1) > 960) {
            $validator->errors()->add('time_to', 'Maximum shift is 16 hours per day');
        }
        $this->request->add([
            'shift_time' => $shiftTime,
            'multi_days' => $days
        ]);
    }
}
