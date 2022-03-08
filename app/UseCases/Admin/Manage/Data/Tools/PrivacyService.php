<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Data\Tools;

use App\Entities\Data\Privacy;
use App\Entities\User\User;

/**
 * Class TermsService
 * Manage terms.
 *
 * @package App\UseCases\Admin\Manage\Data
 */
class PrivacyService
{
    /**
     * @param string $text
     * @param User $user
     * @return Privacy
     */
    public function create(string $text, User $user): Privacy
    {
        $terms = Privacy::create([
            'text' => $text,
            'admin_id' => $user->id
        ]);

        return $terms;
    }
}
