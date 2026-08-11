<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

#[OA\Info(
    version: "1.0.0",
    title: "Midgard API",
    description: "Documentação da API oficial de Midgard (Clone do Instagram)",
    contact: new OA\Contact(email: "guerreiro@midgard.com")
)]
#[OA\Server(
    url: "http://localhost:8000/api",
    description: "Servidor Local"
)]
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}