<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class UserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $user = User::withoutGlobalScopes()->find(Auth::id());

        if ($user->isAdmin() || $user->isOperation()) {
            return;
        }

        $builder->where(function (Builder $query) use ($user) {
            if ($user->isBranchManager()) {
                $query->where('branch_id', $user->branch_id);
            }

            if ($user->isUser() || $user->isAccountant()) {  // Combined for efficiency, assuming mutual exclusivity
                $query->where('id', $user->id);
            }
        })->orWhere('id', $user->id);
    }
}
