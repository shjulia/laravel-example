<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\User\Edit;

use App\Http\Requests\Auth\Practice\Details\BaseDetailsRequest as PracticeBaseDetailsRequest;

/**
 * Class BaseDetailsRequest
 * @package App\Http\Requests\Admin\User\Edit
 */
class BaseDetailsRequest extends PracticeBaseDetailsRequest
{
    public function after($validator)
    {
    }
}
