<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamHasNoFixtures
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Team|null $team */
        $team = $request->route('team');

        if (! $team instanceof Team) {
            return $next($request);
        }

        if ($team->hasFixtures()) {
            return redirect()
                ->back()
                ->with('error', "This action is not allowed because $team->name has active matches going on!");
        }

        return $next($request);
    }
}
