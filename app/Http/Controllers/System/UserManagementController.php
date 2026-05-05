<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Display list of all users
     */
    public function index()
    {
        $users = User::with('shop')
            ->when(request('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })
            ->when(request('role'), function ($query, $role) {
                $query->where('role', $role);
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $roles = ['super_admin', 'admin', 'staff', 'cashier', 'chef', 'user'];
        $shops = Shop::where('status', 'Active')->get();

        return view('system.users', compact('users', 'roles', 'shops'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $roles = ['admin', 'staff', 'cashier', 'chef'];
        $shops = Shop::where('status', 'Active')->get();
        return view('system.users-create', compact('roles', 'shops'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,staff,cashier,chef',
            'shop_id' => 'nullable|exists:shops,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Log activity
        ActivityLog::log('create_user', 'system', "Created new user: {$user->name} ({$user->role})", $user);

        return redirect()->route('system.users')->with('alert', [
            'type' => 'success',
            'message' => "User {$user->name} created successfully!",
        ]);
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        // Prevent editing self through this controller
        if ($user->id === Auth::id()) {
            return redirect()->route('system.users')->with('alert', [
                'type' => 'warning',
                'message' => 'Please use profile page to edit your own account.',
            ]);
        }

        $roles = ['super_admin', 'admin', 'staff', 'cashier', 'chef', 'user'];
        $shops = Shop::where('status', 'Active')->get();

        return view('system.users-edit', compact('user', 'roles', 'shops'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        // Prevent editing self through this controller
        if ($user->id === Auth::id()) {
            return redirect()->route('system.users')->with('alert', [
                'type' => 'error',
                'message' => 'Cannot edit yourself through user management.',
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:super_admin,admin,staff,cashier,chef,user',
            'shop_id' => 'nullable|exists:shops,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Super admin cannot have shop_id
        if ($validated['role'] === 'super_admin') {
            $validated['shop_id'] = null;
        }

        $user->update($validated);

        // Log activity
        ActivityLog::log('update_user', 'system', "Updated user: {$user->name}", $user);

        return redirect()->route('system.users')->with('alert', [
            'type' => 'success',
            'message' => "User {$user->name} updated successfully!",
        ]);
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        // Prevent blocking yourself
        if ($user->id === Auth::id()) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'You cannot block yourself!',
            ]);
        }

        // Prevent blocking other super admins
        if ($user->role === 'super_admin' && $user->id !== Auth::id()) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Cannot block another Super Admin!',
            ]);
        }

        $user->status = $user->status === 'Active' ? 'Inactive' : 'Active';
        $user->save();

        // Log activity
        ActivityLog::log('toggle_user_status', 'system', "Changed {$user->name} status to {$user->status}", $user);

        return back()->with('alert', [
            'type' => 'success',
            'message' => "User {$user->name} is now {$user->status}",
        ]);
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        // Log activity
        ActivityLog::log('reset_password', 'system', "Reset password for user: {$user->name}", $user);

        return back()->with('alert', [
            'type' => 'success',
            'message' => "Password reset successfully for {$user->name}",
        ]);
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'You cannot delete yourself!',
            ]);
        }

        // Prevent deleting other super admins
        if ($user->role === 'super_admin') {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Cannot delete a Super Admin!',
            ]);
        }

        $name = $user->name;
        $user->delete();

        // Log activity
        ActivityLog::log('delete_user', 'system', "Deleted user: {$name}");

        return back()->with('alert', [
            'type' => 'success',
            'message' => "User {$name} deleted successfully!",
        ]);
    }
}
