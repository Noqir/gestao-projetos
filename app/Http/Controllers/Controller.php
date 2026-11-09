<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Controllers são finos: recebem a requisição, delegam (FormRequest, Policy,
 * Service) e escolhem a resposta. Não contêm regra de negócio nem SQL.
 */
abstract class Controller
{
    use AuthorizesRequests;
}
