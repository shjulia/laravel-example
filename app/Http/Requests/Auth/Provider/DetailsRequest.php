<?php

namespace App\Http\Requests\Auth\Provider;

use App\Http\Requests\FormRequest;

/**
 * Class DetailsRequest
 * @package App\Http\Requests\Auth\Provider
 */
class DetailsRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            //'photo' => 'image|mimes:jpg,jpeg,png',
            //'photo' => 'base64image',
            'specialities' => 'nullable|array',
            'routine_tasks' => 'nullable|array',
            /*'availability.*.min' => 'required|numeric|min:0|max:24',
            'availability.*.max' => 'required|numeric|min:0|max:24',
            'availability.*.from' => 'required|regex:/^[0-9\-]{2}:[0-9\-]{2}$/i',*/
            'from.*' => 'required|regex:/^[0-9\-]{2}:[0-9\-]{2}$/i',
            'to.*' => 'required|regex:/^[0-9\-]{2}:[0-9\-]{2}$/i',
            'holiday.*' => 'boolean',
            'state' => 'required|string',
            'city' => 'required|string',
            'zip' => 'required|string',
            'address' => 'required|string',
            'routing_number' => 'nullable|digits_between:9,10',
            'account_number' => 'nullable|digits_between:1,16',
        ];
    }

    /**
     * @param $validator
     */
    public function after($validator)
    {
        if (!($this->user()->specialist->photo ?? false)) {
            $validator->errors()->add('photo', 'Photo must be set');
        }
    }
}

/**
 * @SWG\Definition(
 *    definition="ProviderDetailsRequest",
 *    type="object",
 *    @SWG\Property(
 *          property="specialities",
 *          type="object",
 *          @SWG\Property(property="*", type="integer")
 *    ),
 *     @SWG\Property(
 *          property="delete",
 *          type="boolean",
 *          description="Show do we need reset availability settings (e.g. user has changed availability settings)"
 *    ),
 *    @SWG\Property(
 *          property="from",
 *          required=true,
 *          type="object",
 *          @SWG\Property(property="*", type="string", description="time format is --:--")
 *     ),
 *     @SWG\Property(
 *          property="to",
 *          required=true,
 *          type="object",
 *          @SWG\Property(property="*", type="string", description="time format is --:--")
 *     ),
 *     @SWG\Property(
 *          property="day",
 *          required=true,
 *          type="object",
 *          @SWG\Property(property="*", type="string",
 *          description="days string seperated by ',' like '1,2,3' (mon,tue,wed)")
 *     ),
 *     @SWG\Property(
 *          property="holiday",
 *          required=true,
 *          type="object",
 *          @SWG\Property(property="*", type="boolean")
 *     ),
 * )
 */
