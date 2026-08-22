<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "Official API documentation for the Movie Engine backend architecture.",
    title: "Movie Engine API",
)]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "apiKey",
    description: "Enter token in format (Bearer <token>)",
    name: "Authorization",
    in: "header"
)]
abstract class Controller
{
    //
}
