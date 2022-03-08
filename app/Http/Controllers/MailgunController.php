<?php

namespace App\Http\Controllers;

use App\Services\Mail\MailgunService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MailgunController
{
    protected $mailgunService;

    /**
     * MailgunController constructor.
     *
     * @param MailgunService $mailgunService
     */
    public function __construct(MailgunService $mailgunService)
    {
        $this->mailgunService = $mailgunService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function changeStatus(Request $request)
    {
        try {
            $this->mailgunService->updateStatus($request);
        } catch (\Exception $e) {
            return response([], Response::HTTP_OK);
        }
        return response([], Response::HTTP_OK);
    }
}
