<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_invoices');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can('view_invoices') && ($user->id === $invoice->created_by || $user->hasRole('admin'));
    }

    public function create(User $user): bool
    {
        return $user->can('create_invoices');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can('edit_invoices') && ($user->id === $invoice->created_by || $user->hasRole('admin'));
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->can('delete_invoices') && ($user->id === $invoice->created_by || $user->hasRole('admin'));
    }

    public function restore(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin');
    }

    public function send(User $user, Invoice $invoice): bool
    {
        return $user->can('send_invoices') && ($user->id === $invoice->created_by || $user->hasRole('admin'));
    }

    public function markAsPaid(User $user, Invoice $invoice): bool
    {
        return $user->can('manage_payments') && ($user->id === $invoice->created_by || $user->hasRole('admin'));
    }
}
