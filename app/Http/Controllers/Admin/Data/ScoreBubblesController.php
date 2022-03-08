<?php

namespace App\Http\Controllers\Admin\Data;

use App\Entities\Data\Score;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Data\Review\Score\CreateRequest;
use App\Http\Requests\Admin\Data\Review\Score\EditRequest;
use App\Repositories\Shift\Review\ScoresRepository;
use App\UseCases\Admin\Manage\Data\Review\ScoresService;

/**
 * Class ScoreBubblesController
 * @package App\Http\Controllers\Admin\Data
 */
class ScoreBubblesController extends Controller
{
    /**
     * @var ScoresRepository
     */
    private $scoresRepository;

    /**
     * @var ScoresService
     */
    private $scoresService;

    /**
     * ScoreBubblesController constructor.
     * @param ScoresRepository $scoresRepository
     * @param ScoresService $scoresService
     */
    public function __construct(
        ScoresRepository $scoresRepository,
        ScoresService $scoresService
    ) {
        $this->scoresRepository = $scoresRepository;
        $this->scoresService = $scoresService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $scores = $this->scoresRepository->findAll();
        return view('admin.data.scores.index', compact('scores'));
    }

    /**
     * @param Score $score
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Score $score)
    {
        return view('admin.data.scores.show', compact('score'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.data.scores.create');
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        try {
            $score = $this->scoresService->create($request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.scores.show', $score);
    }

    /**
     * @param Score $score
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Score $score)
    {
        return view('admin.data.scores.edit', compact('score'));
    }

    /**
     * @param EditRequest $request
     * @param Score $score
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EditRequest $request, Score $score)
    {
        try {
            $score = $this->scoresService->edit($score, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.scores.show', $score);
    }

    /**
     * @param Score $score
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Score $score)
    {
        try {
            $this->scoresService->destroy($score);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.scores.index');
    }
}
