<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): JsonResponse|BinaryFileResponse
    {
        $result = $next($request);

        return $result instanceof BinaryFileResponse ? $result : response()->json([
            'data' => $result->isSuccessful() ?
                $result->original :
                ['error' => $result->original ? $result?->original['message'] : $result?->original],
        ], $result->status());

    }
}
