<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter\NewsLetter\Create;

use App\Http\Requests\FormRequest;

class Command extends FormRequest
{
    public function rules(): array
    {
        return [
            'template' => 'required|int|exists:newsletter_templates,id',
            'subject' => 'required|string',
            'start_date' => 'required|date',
            'emails.*' => 'required|email',
            'role' => 'nullable|int|exists:roles,id'
        ];
    }
}
