<?php

declare(strict_types=1);

namespace App\Entities\Payment\Invoices;

use App\Entities\Payment\ProviderCharge;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * @deprecated
 * Class ProviderChargesExport - Export Provider charges for period
 * @package App\Entities\Payment\Invoices
 */
class ProviderChargesExport implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize
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
        $query = ProviderCharge::query()
            ->with(['provider.user'])
            ->orderBy('id', 'DESC');
        if ($this->from) {
            $query->where('created_at', '>=', $this->from);
        }
        if ($this->to) {
            $query->where('created_at', '<=', $this->to);
        }
        return $query;
    }

    /**
     * @param ProviderCharge $charge
     * @return array
     * @psalm-suppress UndefinedFunction
     */
    public function map($charge): array
    {
        return [
            $charge->shift_id,
            $charge->provider->user->full_name,
            $charge->charge_id,
            $charge->payment_system,
            $charge->payment_status ?: $charge->status,
            $charge->amount,
            formatedTimestamp($charge->created_at),
            formatedTimestamp($charge->updated_at)
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Shift',
            'Provider',
            'PS charge id',
            'Payment system',
            'Payment status',
            'Amount',
            'Created at',
            'Updated at'
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_CURRENCY_USD,
        ];
    }
}
