<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_expenses');
    }

    public function view(User $user, Expense $expense): bool
    {
        return $user->can('view_expenses') && ($user->id === $expense->created_by || $user->hasRole('admin'));
    }

    public function create(User $user): bool
    {
        return $user->can('create_expenses');
    }

    public function update(User $user, Expense $expense): bool
    {
        return $user->can('edit_expenses') && ($user->id === $expense->created_by || $user->hasRole('admin'));
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->can('delete_expenses') && ($user->id === $expense->created_by || $user->hasRole('admin'));
    }

    public function restore(User $user, Expense $expense): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Expense $expense): bool
    {
        return $user->hasRole('admin');
    }
}
