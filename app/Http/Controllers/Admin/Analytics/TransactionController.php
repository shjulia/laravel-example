<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Analytics;

use App\Entities\Payment\Invoices\PracticesChargesExport;
use App\Entities\Payment\Invoices\ProviderChargesExport;
use App\Http\Controllers\Controller;
use App\Repositories\Invite\InviteRepository;
use App\Repositories\Payment\ChargeRepository;
use App\Repositories\Payment\ProviderBonusesRepository;
use App\Repositories\Payment\ProviderChargeRepository;
use Illuminate\Http\Request;

/**
 * Class TransactionController
 * @package App\Http\Controllers\Admin\Analytics
 */
class TransactionController extends Controller
{
    /**
     * @var ChargeRepository
     */
    private $practiceChargeRepository;

    /**
     * TransactionController constructor.
     * @param ChargeRepository $practiceChargeRepository
     */
    public function __construct(ChargeRepository $practiceChargeRepository)
    {
        $this->practiceChargeRepository = $practiceChargeRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function practices()
    {
        $charges = $this->practiceChargeRepository->findAllPaginate();
        $tab = 'practices';
        return view('admin.analytics.transactions.practices', compact('charges', 'tab'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportPracticesCharges(Request $request)
    {
        $title = "practices-charges_" . $request->from . '_-_' . $request->to;
        return (new PracticesChargesExport($request->from, $request->to))
            ->download($title . '.xlsx');
    }
}
