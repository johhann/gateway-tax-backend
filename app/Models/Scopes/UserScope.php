<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class UserScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Avoid recursion: Skip during container authentication resolution
        if ($this->isResolvingAuth()) {
            return;
        }

        $authUser = $this->resolveAuthUser();
        if (! $authUser) {
            return;
        }

        $user = $model->newQueryWithoutScopes()->find($authUser->getKey());
        if (! $user) {
            return;
        }

        if ($user->isAdmin() || $user->isOperation()) {
            return;
        }

        $builder->where(function ($q) use ($user) {
            if ($user->isBranchManager()) {
                $q->where('branch_id', $user->branch_id);
            }

            if ($user->isUser() || $user->isAccountant()) {
                $q->where('id', $user->id);
            }
        });
    }

    private function isResolvingAuth(): bool
    {
        // Detect if we are inside Filament/Auth guards resolving user model
        return App::runningInConsole()
            || str_contains(debug_backtrace()[1]['class'] ?? '', 'AuthManager')
            || request()->routeIs('filament.*') && ! Auth::check();
    }

    private function resolveAuthUser()
    {
        return Auth::guard('filament')->user()
            ?? Auth::guard('web')->user()
            ?? Auth::guard('sanctum')->user()
            ?? Auth::user();
    }
}
