<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TaxRequestScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = User::find(Auth::id());

        if ($user->isBranchManager()) {
            $builder
                ->where('assigned_user_id', $user->id)
                ->orWhereRelation('assignedUser', 'branch_id', $user->branch_id);
        }

        if ($user->isUser()) {
            $builder->where('user_id', $user->id);
        }

        if ($user->isAccountant()) {
            $builder->where('assigned_user_id', $user->id);
        }
    }
}
