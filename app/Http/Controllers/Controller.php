<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Base controller class for the application.
 *
 * Provides common functionality to all controllers through Laravel traits:
 * - AuthorizesRequests: Gate and policy authorization
 * - ValidatesRequests: Request validation helpers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
