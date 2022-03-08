<?php

declare(strict_types=1);

namespace App\Entities\Payment\Invoices;

use App\Entities\Shift\Shift;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * @deprecated
 * Class PracticesChargesExport - Export Practices Charges for period
 * @package App\Entities\Payment\Invoices
 */
class PracticesChargesExport implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    use Exportable;

    /**
     * @var string
     */
    private $from;
    /**
     * @var string
     */
    private $to;

    public function __construct(?string $from = null, ?string $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function query()
    {
        $query = Shift::query()
            ->with(['charges'])
            ->whereIn('id', [1912236,
                1912233,
                1912232,
                1912231,
                1912223,
                1912221,
                1912219,
                1912216,
                1912213,
                1912210,
                1912207,
                1912206,
                1912197,
                1912192,
                1912189,
                1912185,
                1912181,
                1912172,
                1912170,
                1912168,
                1912165,
                1912166,
                1912158,
                1912150,
                1912144,
                1912143,
                1912126,
                1912122,
                1912117,
                1912114,
                1912067,
                1912065,
                1912062,
                1912058,
                1912045,
                1912004])
            ->orderBy('id', 'DESC');
        if ($this->from) {
            $query->where('created', '>=', $this->from);
        }
        if ($this->to) {
            $query->where('created', '<=', $this->to);
        }
        return $query;
    }

    /**
     * @param Shift $shift
     * @return array
     * @psalm-suppress UndefinedFunction
     */
    public function map($shift): array
    {
        return [
            $shift->providersName(),
            $shift->period(),
            $shift->cost_for_practice,
            $shift->cost
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Provider',
            'Worked datetime',
            'Cost',
            'Paid to provider'
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_CURRENCY_USD,
            'D' => NumberFormat::FORMAT_CURRENCY_USD
        ];
    }
}
