<?php

declare(strict_types=1);

namespace App\Entities\User\Export;

use App\Entities\User\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Class Users - Export all users to excel
 * @package App\Entities\User\Export
 */
class Users implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function query()
    {
        return User::query()
            ->active()
            ->where('is_test_account', 0)
            ->orderBy('id', 'DESC')
            ->with(['practices', 'specialist', 'roles']);
    }

    /**
     * @param User $user
     * @return array
     * @psalm-suppress UndefinedFunction
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->full_name,
            $user->email,
            $this->getStatusesList($user),
            $this->getRolesList($user)
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Full name',
            'Email',
            'Status',
            'Role'
        ];
    }

    /**
     * @param User $user
     * @return string
     */
    private function getRolesList(User $user): string
    {
        $roles = [];
        foreach ($user->roles as $role) {
            $roles[] = $role->title;
        }
        return implode(', ', $roles);
    }

    /**
     * @param User $user
     * @return string
     */
    private function getStatusesList(User $user): string
    {
        $statuses = [];
        if ($provider = $user->specialist) {
            if ($provider->isApproved()) {
                $statuses[] = "Provider: Active";
            } elseif ($provider->isWaiting()) {
                $statuses[] = "Provider: Waiting";
            } elseif ($provider->isDuplicate()) {
                $statuses[] = "Provider: Duplicate";
            }
        }
        if ($practice = ($user->practices[0] ?? null)) {
            if ($practice->isApproved()) {
                $statuses[] = "Practice: Active";
            } elseif ($practice->isWaiting()) {
                $statuses[] = "Practice: Waiting";
            }
        }
        return implode(', ', $statuses);
    }
}
