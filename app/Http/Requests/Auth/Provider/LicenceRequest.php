<?php

namespace App\Http\Requests\Auth\Provider;

use App\Entities\User\User;
use App\Http\Requests\FormRequest;

/**
 * Class Step4Request
 * @package App\Http\Requests\Auth\Provider
 */
class LicenceRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type.*' => 'nullable|string:50',
            'state.*' => 'nullable|string:3|exists:states,short_title',
            'number.*' => 'nullable|string:255',
            'expiration_date.*' => 'nullable|date',
            'position.*' => 'integer'
        ];
    }

    /**
     * @param $validator
     */
    public function after($validator)
    {
        $user = $this->getUserByParams();

        for ($i = 0; $i < count($this->position); $i++) {
            if (!isset($this->position[$i])) {
                continue;
            }
            if (isset($user->specialist->licenses[$i]->photo) && $user->specialist->licenses[$i]->photo) {
                continue;
            }
            if (!$this->type[$i]) {
                $validator->errors()->add('type.' . $i, 'Type must be set');
            }
            if (!$this->state[$i]) {
                $validator->errors()->add('state.' . $i, 'State must be set');
            }
            if (!$this->number[$i]) {
                $validator->errors()->add('number.' . $i, 'Number must be set');
            }
            if (!$this->expiration_date[$i]) {
                $validator->errors()->add('expiration_date.' . $i, 'Expiration date must be set');
            }
        }
    }

    /**
     * @return User
     */
    private function getUserByParams(): User
    {
        if (!$this->code) {
            return auth()->user();
        }
        return User::where('tmp_token', $this->code)->with('specialist.licenses')->first();
    }
}

/**
 * @SWG\Definition(
 *     definition="MedicalLicenceRequest",
 *     type="object",
 *     @SWG\Property(
 *          property="type",
 *          type="object",
 *          required=true,
 *          @SWG\Property(property="*", type="integer")
 *     ),
 *     @SWG\Property(
 *          property="state",
 *          type="object",
 *          required=true,
 *          @SWG\Property(property="*", type="string")
 *     ),
 *     @SWG\Property(
 *          property="number",
 *          type="object",
 *          required=true,
 *          @SWG\Property(property="*", type="string")
 *     ),
 *     @SWG\Property(
 *          property="position",
 *          type="object",
 *          required=true,
 *          @SWG\Property(property="*", type="integer")
 *     ),
 *     @SWG\Property(
 *          property="expiration_date",
 *          type="object",
 *          required=true,
 *          @SWG\Property(property="*", type="string")
 *     )
 * )
 */
