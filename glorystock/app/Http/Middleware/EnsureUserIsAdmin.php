<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Only allow users with the 'admin' role (legacy access_denied.php behaviour).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            // API requests (Bearer token / Accept: application/json) get a 403;
            // web requests get the legacy redirect to the access-denied page.
            $isApi = $request->expectsJson() && $request->is('api/*') || $request->bearerToken();

            if ($isApi) {
                return response()->json(['message' => 'Admin access required.'], 403);
            }

            return redirect()->route('access.denied');
        }

        return $next($request);
    }
}
