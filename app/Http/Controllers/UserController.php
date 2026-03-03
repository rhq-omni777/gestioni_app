<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = Auth::user()?->role;
            if (! in_array($role, ['admin', 'manager'], true)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $query = User::query();

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->string('role')->toString()) {
            $query->where('role', $role);
        }

        if (($active = $request->string('active')->toString()) !== '') {
            $query->where('is_active', $active === '1');
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,manager,user'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Evitar auto-desactivarse o bajarse de rol sin querer
        if ($user->id === Auth::id()) {
            $data['is_active'] = true;
            $data['role'] = $user->role; // no permitir que se cambie a sí mismo
        }

        $data['is_active'] = $data['is_active'] ?? false;

        $user->update($data);

        return back()->with('status', 'Usuario actualizado');
    }
}
