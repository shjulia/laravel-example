<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Data\Rate;

use App\Http\Requests\FormRequest;

/**
 * Class CreateRequest
 * @package App\Http\Requests\Admin\Data\Rate
 */
class CreateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'position.*' => 'required|integer|exists:positions,id',
            'rate.*' => 'required|numeric|min:1',
            'minimum_profit.*' => 'required|numeric|min:1',
            'surge_price.*' => 'required|numeric|min:1',
            'max_day_rate.*' => 'nullable|numeric|min:1'
        ];
    }

    /**
     * @param int $key
     * @return array
     */
    public function getPivotAttributes(int $key): array
    {
        return [
            'rate' => $this->rate[$key],
            'minimum_profit' => $this->minimum_profit[$key],
            'surge_price' => $this->surge_price[$key],
            'max_day_rate' => $this->max_day_rate[$key] ?? null
        ];
    }
}
