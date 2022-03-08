<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter\Template\Create;

use App\Http\Requests\FormRequest;

class Command extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'html_content' => 'required|string',
            'json_content' => 'required|string'
        ];
    }
}
