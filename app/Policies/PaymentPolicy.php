<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_payments');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->can('view_payments') && ($user->id === $payment->created_by || $user->hasRole('admin'));
    }

    public function create(User $user): bool
    {
        return $user->can('create_payments');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->can('edit_payments') && ($user->id === $payment->created_by || $user->hasRole('admin'));
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->can('delete_payments') && ($user->id === $payment->created_by || $user->hasRole('admin'));
    }

    public function restore(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }
}
