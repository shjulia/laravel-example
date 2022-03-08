<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\NewsLetter;

use App\Entities\NewsLetter\Template;
use App\Http\Controllers\Controller;
use App\UseCases\NewsLetter\Template as TemplateUC;
use Illuminate\Http\Response;

class TemplateController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $templates = Template::orderBy('id', 'DESC')->get();
        return view('admin.newsletter.template.index', compact('templates'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.newsletter.template.create');
    }

    public function store(TemplateUC\Create\Command $command, TemplateUC\Create\Handler $handler)
    {
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    public function edit(Template $template)
    {
        return view('admin.newsletter.template.edit', compact('template'));
    }

    public function update(Template $template, TemplateUC\Edit\Command $command, TemplateUC\Edit\Handler $handler)
    {
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    public function destroy(Template $template, TemplateUC\Remove\Handler $handler)
    {
        try {
            $handler->handle(new TemplateUC\Remove\Command($template->id));
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.newsletter.template.index');
    }
}
