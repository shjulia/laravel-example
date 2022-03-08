<?php

declare(strict_types=1);

namespace App\Repositories\Data;

use App\Entities\Data\Term;

/**
 * Class TermsRepository
 * @package App\Repositories\Data
 */
class TermsRepository
{
    /**
     * @return Term[]
     */
    public function findAll()
    {
        return Term::orderBy('id', 'DESC')->with('admin')->get();
    }

    /**
     * @return Term
     */
    public function getLast(): Term
    {
        $terms = Term::orderBy('id', 'DESC')->first();
        if (!$terms) {
            throw new \DomainException('Terms not found');
        }
        return $terms;
    }
}
