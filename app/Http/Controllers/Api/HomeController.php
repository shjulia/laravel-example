<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * Class HomeController
 * @package App\Http\Controllers\Api
 *
 * @SWG\Swagger(
 *     basePath="/api",
 *     host="nextondeck.doingboon.com",
 *     schemes={"https"},
 *     produces={"application/json", "multipart/form-data"},
 *     consumes={"application/json", "multipart/form-data"},
 *     @SWG\Info(
 *         version="0.0.1",
 *         title="Boon api for mobile devices",
 *         description="HTTP JSON API",
 *     ),
 *     @SWG\SecurityScheme(
 *         securityDefinition="OAuth2",
 *         type="oauth2",
 *         flow="password",
 *         tokenUrl="https://nextondeck.doingboon.com/api/oauth/token"
 *     ),
 *     @SWG\SecurityScheme(
 *         securityDefinition="Bearer",
 *         type="apiKey",
 *         name="Authorization",
 *         in="header"
 *     ),
 *     @SWG\Definition(
 *         definition="ErrorModel",
 *         type="object",
 *         required={"code", "message"},
 *         @SWG\Property(
 *             property="code",
 *             type="integer",
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *         )
 *     )
 * )
 */
class HomeController extends Controller
{
    /**
     * @return array
     *
     * @SWG\Get(
     *     path="/",
     *     tags={"Info"},
     *     @SWG\Response(
     *         response="200",
     *         description="API info",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="title", type="string"),
     *             @SWG\Property(property="version", type="string")
     *         ),
     *     )
     * )
     */
    public function index()
    {
        return [
          'title' => 'Boon api for mobile devices',
          'version' => '0.0.1'
        ];
    }
}
