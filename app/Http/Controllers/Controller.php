<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Minha API",
    description: "Documentação completa da minha API Laravel",
    contact: new OA\Contact(
        name: "Suporte",
        email: "suporte@example.com",
        url: "https://example.com/suporte"
    ),
    license: new OA\License(
        name: "Apache 2.0",
        url: "https://www.apache.org/licenses/LICENSE-2.0.html"
    )
)]
#[OA\Server(
    url: "http://localhost:8000/api/v1",
    description: "Servidor local de desenvolvimento"
)]
#[OA\Server(
    url: "https://api.example.com/api/v1",
    description: "Servidor de produção"
)]

abstract class Controller
{
    //
}
