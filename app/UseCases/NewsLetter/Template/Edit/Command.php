<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter\Template\Edit;

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
            'id' => 'required|int|exists:newsletter_templates,id',
            'title' => 'required|string',
            'html_content' => 'required|string',
            'json_content' => 'required|string'
        ];
    }

    protected function validationData()
    {
        $this->id = $this->template->id ?? null;
        return array_merge(['id' => $this->id], $this->all());
    }
}
