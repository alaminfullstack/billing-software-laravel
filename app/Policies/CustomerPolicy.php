<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_customers');
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->can('view_customers') && ($user->id === $customer->user_id || $user->hasRole('admin'));
    }

    public function create(User $user): bool
    {
        return $user->can('create_customers');
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->can('edit_customers') && ($user->id === $customer->user_id || $user->hasRole('admin'));
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->can('delete_customers') && ($user->id === $customer->user_id || $user->hasRole('admin'));
    }

    public function restore(User $user, Customer $customer): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Customer $customer): bool
    {
        return $user->hasRole('admin');
    }
}
