<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Data;

use App\Entities\Data\Term;
use App\Entities\User\User;

/**
 * Class TermsService
 * Manage terms.
 *
 * @package App\UseCases\Admin\Manage\Data
 */
class TermsService
{
    /**
     * @param string $text
     * @param User $user
     * @return Term
     */
    public function create(string $text, User $user): Term
    {
        $terms = Term::create([
            'text' => $text,
            'admin_id' => $user->id
        ]);

        return $terms;
    }
}
