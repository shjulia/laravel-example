<?php

declare(strict_types=1);

namespace App\Entities\User\Export;

use App\Entities\User\SignupAutosave;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Class Autosaves - Export all autosaves to excel
 * @package App\Entities\User\Export
 */
class Autosaves implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function query()
    {
        return SignupAutosave::query()
            ->orderBy('id', 'DESC');
    }

    /**
     * @param SignupAutosave $user
     * @return array
     * @psalm-suppress UndefinedFunction
     */
    public function map($user): array
    {
        return [
            $user->email,
            $user->first_name,
            $user->last_name
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Email',
            'First name',
            'Last Name'
        ];
    }
}
