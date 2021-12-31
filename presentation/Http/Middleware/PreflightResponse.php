<?php
declare(strict_types=1);

namespace Presentation\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PreflightResponse
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->getMethod() === "OPTIONS") {
            return new Response(
                "No content", 204,
                [
                    "Access-Control-Allow-Methods" => "*",
                    "Access-Control-Allow-Origin"  => "*",
                    "Access-Control-Allow-Headers" => "*",
                    "Access-Control-Max-Age"       => 86400,
                ]
            );
        }
        /** @var Response $response */
        $response = $next($request);

        $response->withHeaders(
            [
                "Access-Control-Allow-Methods" => "*",
                "Access-Control-Allow-Origin"  => "*",
                "Access-Control-Allow-Headers" => "*",
                "Access-Control-Max-Age"       => 86400,
            ]
        );

        return $response;
    }
}
