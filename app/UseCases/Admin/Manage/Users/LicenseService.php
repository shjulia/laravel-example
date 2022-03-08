<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Users;

use App\Entities\User\License;

/**
 * Class LicenseService
 * Approves or declines license.
 *
 * @package App\UseCases\Admin\Manage\Users
 */
class LicenseService
{
    /**
     * @param License $license
     */
    public function approve(License $license): void
    {
        if ($license->isApproved()) {
            throw new \DomainException('License is already approved');
        }
        try {
            $license->approve();
            $license->save();
        } catch (\Exception $e) {
            throw new \DomainException('License approving error');
        }
    }

    /**
     * @param License $license
     * @param string|null $reason
     */
    public function decline(License $license, ?string $reason): void
    {
        if ($license->isDeclined()) {
            throw new \DomainException('License is already declined');
        }
        try {
            $license->decline($reason);
            $license->save();
        } catch (\Exception $e) {
            throw new \DomainException('License decline error');
        }
    }

    /**
     * @param License $license
     */
    public function setBaseStatus(License $license): void
    {
        if ($license->isBaseStatus()) {
            throw new \DomainException('License already has base status');
        }
        try {
            $license->setBaseStatus();
            $license->save();
        } catch (\Exception $e) {
            throw new \DomainException('License changing error');
        }
    }
}
