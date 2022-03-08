<?php

declare(strict_types=1);

namespace App\Entities\DTO;

/**
 * Class SmsDTO - DTO to transfer data for sms notification
 * @package App\Entities\DTO
 */
class SmsDTO
{
    /**
     * @var string|null
     */
    public $number;

    /**
     * @var string
     */
    public $text;

    /**
     * @var string|null
     */
    public $link;

    /**
     * SmsDTO constructor.
     * @param string $number
     * @param string $text
     * @param string|null $link
     */
    public function __construct(string $text, ?string $link = null, ?string $number = null)
    {
        $this->text = $text;
        $this->link = $link;
        $this->number = $number;
    }
}
