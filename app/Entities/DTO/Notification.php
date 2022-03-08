<?php

declare(strict_types=1);

namespace App\Entities\DTO;

/**
 * Class Notification - DTO for transfer notification data
 * @package App\Entities\DTO
 */
class Notification
{
    /** @var string */
    public $title;

    /** @var string | null */
    public $text;

    /** @var string | null */
    public $link;

    /** @var integer */
    public $user;

    /** @var integer | null */
    public $from;

    /** @var string | null */
    public $icon;

    /** @var string | null */
    public $read_at;

    public function __construct(
        string $title,
        int $user,
        ?int $from = null,
        ?string $text = null,
        ?string $link = null,
        ?string $icon = null
    ) {
        $this->title = $title;
        $this->user = $user;
        $this->from = $from ?: null;
        $this->text = $text ?: null;
        $this->link = $link ?: null;
        $this->icon = $icon ?: null;
    }

    public function getArray()
    {
        return [
            'title' => $this->title,
            'user' => $this->user,
            'from' => $this->from,
            'text' => $this->text,
            'link' => $this->link,
            'icon' => $this->icon
        ];
    }
}
