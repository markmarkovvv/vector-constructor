<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    private array $allowedOrigins = [
        'https://app.stellarweb.icu',
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost:3000',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin', '');

        if ($request->isMethod('OPTIONS')) {
            $response = response('', 204);
            return $this->addCorsHeaders($response, $origin);
        }

        /** @var Response $response */
        $response = $next($request);

        return $this->addCorsHeaders($response, $origin);
    }

    private function addCorsHeaders(Response $response, string $origin): Response
    {
        $allowOrigin = in_array($origin, $this->allowedOrigins, true)
            ? $origin
            : (app()->isLocal() ? '*' : $this->allowedOrigins[0]);

        $response->headers->set('Access-Control-Allow-Origin', $allowOrigin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, X-CSRF-TOKEN');
        $response->headers->set('Access-Control-Max-Age', '86400');

        return $response;
    }
}
