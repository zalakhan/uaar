<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class AccountPasswordController extends Controller
{
    /**
     * Show the form for the logged-in super admin to change their password.
     */
    public function edit(Request $request): View
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        return view('admin.account.password');
    }

    /**
     * Update the logged-in super admin's password.
     */
    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('admin.account.password.edit')
            ->with('success', 'Your password has been updated successfully.');
    }
}
