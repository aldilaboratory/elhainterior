<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone'    => 'required|string|max:30',
            'address'  => 'required|string|max:255',
            'password' => 'required|confirmed|min:8',

            // dari autocomplete
            'destination_id'    => 'nullable|integer|min:1',
            'destination_label' => 'nullable|string|max:255',
            'postal_code'       => 'nullable|string|max:20',
        ]);

        // Buat user
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        // Opsional: buat Address default dari data register
        Address::create([
            'user_id'           => $user->id,
            'label'             => 'Alamat Utama',
            'recipient_name'    => $user->name,
            'phone'             => $validated['phone'],
            'address_line'      => $validated['address'],
            'destination_id'    => $validated['destination_id'] ?? null,
            'destination_label' => $validated['destination_label'] ?? null,
            'postal_code'       => $validated['postal_code'] ?? null,
            'is_default'        => true,
        ]);

        event(new Registered($user));
        auth()->login($user);

        return redirect()->route('customer.home');
    }
}
