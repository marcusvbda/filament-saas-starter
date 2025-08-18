<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $exceptions = ['filament.admin.auth.email-verification.verify'];
            $user = auth()->user();
            $routeName = $request->route()->getName();
            if (!$user->hasRole('admin') && !$user->hasRole('super_admin') && !in_array($routeName, $exceptions)) {
                Auth::logout();
                Notification::make()
                    ->title('Acesso negado')
                    ->body('Você não tem permissão para acessar esta área.')
                    ->danger()
                    ->send();
                return redirect()->back();
            }
        }

        return $next($request);
    }
}
