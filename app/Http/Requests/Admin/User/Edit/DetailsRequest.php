<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\User\Edit;

use App\Http\Requests\Auth\Provider\DetailsRequest as ProviderDetailsRequest;

/**
 * Class DetailsRequest
 * @package App\Http\Requests\Admin\User\Edit
 */
class DetailsRequest extends ProviderDetailsRequest
{
    public function after($validator)
    {
    }
}
