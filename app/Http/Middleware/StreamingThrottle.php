<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class StreamingThrottle
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);

        $maxAttempts = (int) $maxAttempts;
        $decayMinutes = (int) $decayMinutes;

        if (Cache::has($key . ':timer')) {
            return response()->json([
                'success' => false,
                'message' => 'Too many streaming requests. Please try again later.'
            ], 429);
        }

        if (Cache::get($key, 0) >= $maxAttempts) {
            $retryAfter = Cache::get($key . ':timer');
            $availableIn = $retryAfter - time();

            return response()->json([
                'success' => false,
                'message' => 'Too many streaming requests.',
                'retry_after' => $availableIn
            ], 429);
        }

        Cache::add($key, 0, $decayMinutes * 60);
        Cache::add($key . ':timer', time() + ($decayMinutes * 60), $decayMinutes * 60);

        $response = $next($request);

        Cache::increment($key);

        return $response;
    }

    /**
     * Resolve request signature.
     */
    protected function resolveRequestSignature($request)
    {
        return sha1(
            $request->method() .
            '|' . $request->server('SERVER_NAME') .
            '|' . $request->path() .
            '|' . $request->ip() .
            '|' . $request->user()?->id
        );
    }
}