<?php

declare(strict_types=1);

namespace App\Http\Controllers\StaticData;

use App\Http\Controllers\Controller;
use App\Repositories\Data\PrivacyRepository;
use App\Repositories\Data\TermsRepository;

/**
 * Class StaticController
 * @package App\Http\Controllers\StaticData
 */
class StaticController extends Controller
{
    private $termsRepository;
    private $privacyRepository;

    public function __construct(TermsRepository $termsRepository, PrivacyRepository $privacyRepository)
    {
        $this->termsRepository = $termsRepository;
        $this->privacyRepository = $privacyRepository;
    }

    public function terms()
    {
        $terms = $this->termsRepository->getLast();
        return view('static.terms', compact('terms'));
    }

    public function privacy()
    {
        $privacy = $this->privacyRepository->getLast();
        return view('static.privacy', compact('privacy'));
    }

    public function fcra()
    {
        return view('static.fcra');
    }
}
