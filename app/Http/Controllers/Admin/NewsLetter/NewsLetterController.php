<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\NewsLetter;

use App\Entities\NewsLetter\NewsLetter;
use App\Entities\NewsLetter\Template;
use App\Http\Controllers\Controller;
use App\Repositories\User\RolesRepository;
use App\UseCases\NewsLetter\NewsLetter as NewsLetterUC;
use Illuminate\Http\Response;

class NewsLetterController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $newsLetters = NewsLetter::with('template')->orderBy('id', 'DESC')->paginate();
        return view('admin.newsletter.newsletter.index', compact('newsLetters'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(RolesRepository $rolesRepo)
    {
        $templates = Template::orderBy('id', 'DESC')->get();
        $roles = $rolesRepo->findWorkRoles();
        return view('admin.newsletter.newsletter.create', compact('templates', 'roles'));
    }

    public function store(NewsLetterUC\Create\Command $command, NewsLetterUC\Create\Handler $handler)
    {
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    public function edit(NewsLetter $newsletter, RolesRepository $rolesRepo)
    {
        $templates = Template::orderBy('id', 'DESC')->get();
        $roles = $rolesRepo->findWorkRoles();
        return view('admin.newsletter.newsletter.edit', compact('newsletter', 'templates', 'roles'));
    }

    public function update(
        NewsLetter $newsletter,
        NewsLetterUC\Edit\Command $command,
        NewsLetterUC\Edit\Handler $handler
    ) {
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    public function destroy(NewsLetter $newsletter, NewsLetterUC\Remove\Handler $handler)
    {
        try {
            $handler->handle(new NewsLetterUC\Remove\Command($newsletter->id));
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.newsletter.newsletter.index');
    }
}
