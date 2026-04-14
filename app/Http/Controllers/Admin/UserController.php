<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $users = User::with('school')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('school_id', $user->school_id))
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $schools = $user->isSuperAdmin()
            ? School::active()->orderBy('name')->get()
            : School::where('id', $user->school_id)->get();

        $roles = $user->isSuperAdmin()
            ? ['super_admin', 'admin', 'operator']
            : ['admin', 'operator'];

        return view('admin.users.create', compact('schools', 'roles'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'school_id' => 'nullable|exists:schools,id',
            'role' => 'required|in:super_admin,admin,operator',
        ]);

        if (!$user->isSuperAdmin()) {
            $validated['school_id'] = $user->school_id;
            if ($validated['role'] === 'super_admin') {
                abort(403);
            }
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(Request $request, User $user)
    {
        $currentUser = $request->user();
        if (!$currentUser->isSuperAdmin() && $user->school_id != $currentUser->school_id) {
            abort(403);
        }

        $schools = $currentUser->isSuperAdmin()
            ? School::active()->orderBy('name')->get()
            : School::where('id', $currentUser->school_id)->get();

        $roles = $currentUser->isSuperAdmin()
            ? ['super_admin', 'admin', 'operator']
            : ['admin', 'operator'];

        return view('admin.users.edit', compact('user', 'schools', 'roles'));
    }

    public function update(Request $request, User $targetUser)
    {
        $currentUser = $request->user();
        if (!$currentUser->isSuperAdmin() && $targetUser->school_id != $currentUser->school_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $targetUser->id,
            'password' => 'nullable|string|min:8|confirmed',
            'school_id' => 'nullable|exists:schools,id',
            'role' => 'required|in:super_admin,admin,operator',
        ]);

        if (!$currentUser->isSuperAdmin()) {
            $validated['school_id'] = $currentUser->school_id;
            if ($validated['role'] === 'super_admin') {
                abort(403);
            }
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $targetUser->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(Request $request, User $targetUser)
    {
        $currentUser = $request->user();

        if ($currentUser->id === $targetUser->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        if (!$currentUser->isSuperAdmin() && $targetUser->school_id != $currentUser->school_id) {
            abort(403);
        }

        $targetUser->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}
