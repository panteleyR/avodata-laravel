<?php

namespace App\Http\Middleware;

use App\Exceptions\ApplicationException;
use App\Exceptions\NoAuthorizateException;
use App\Models\Cabinet;
use Closure;
use App\Exceptions\Base\AuthorizationException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;

class CabinetAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (!isset($request->cabinetId)) {
            throw new \Exception('inccorrect cabinetId', 422);
        }

        $cabinet = Cabinet::find($request->cabinetId);
        if (!$cabinet) {
            throw new \Exception('Cabinet doesn\'t exist', 404);
        }

        $id = $request->cabinetId;

        if ( ! $request->user() ||
            (! $request->user()->isSuperAdmin() &&
                (! $request->user()->currentMember() || ! $request->user()->currentMember()->cabinet->id === $id)
            )) {
            throw new AuthorizationException();
        }
        return $next($request);
    }
}
