<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter\NewsLetter\Edit;

use App\Http\Requests\FormRequest;

class Command extends FormRequest
{
    /**
     * @var int|null
     */
    public $id;

    public function rules(): array
    {
        return [
            'id' => 'required|int|exists:newsletter_newsletters,id',
            'template' => 'required|int|exists:newsletter_templates,id',
            'subject' => 'required|string',
            'start_date' => 'required|date',
            'emails.*' => 'required|email',
            'role' => 'nullable|int|exists:roles,id'
        ];
    }

    protected function validationData()
    {
        $this->id = $this->newsletter->id ?? null;
        return array_merge(['id' => $this->id], $this->all());
    }
}
