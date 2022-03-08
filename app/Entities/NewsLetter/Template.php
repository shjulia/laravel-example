<?php

declare(strict_types=1);

namespace App\Entities\NewsLetter;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Template
 * @property int $id
 * @property string $title
 * @property string $html_content
 * @property string $json_content
 * @property Carbon $created_at
 */
class Template extends Model
{
    protected $guarded = [];

    protected $table = 'newsletter_templates';

    public static function create(
        string $title,
        string $htmlContent,
        string $jsonContent
    ): self {
        $template = new self();
        $template->title = $title;
        $template->html_content = $htmlContent;
        $template->json_content = $jsonContent;
        return $template;
    }

    public function edit(string $title, string $htmlContent, string $jsonContent): void
    {
        $this->title = $title;
        $this->html_content = $htmlContent;
        $this->json_content = $jsonContent;
    }
}
