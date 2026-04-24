<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $staff = User::when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
            })
            ->when($request->role, function ($query) use ($request) {
                $query->where('role', $request->role);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:admin,cashier,staff',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('staff.index')->with('success', 'Staff member added successfully!');
    }

    public function edit($id)
    {
        $staff = User::findOrFail($id);
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        $currentUser = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($staff->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:admin,cashier,staff',
            'password' => 'nullable|string|min:6|confirmed',
        ];

        // Only admin can change status
        if ($currentUser->role === 'admin') {
            // System Owner (ID: 1) can change any status
            // Other Admin can only change non-admin status or their own status
            if ($currentUser->id === 1 || $staff->role !== 'admin' || $staff->id === $currentUser->id) {
                $rules['status'] = 'required|in:active,inactive';
            }
        }

        $validated = $request->validate($rules);

        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->phone = $validated['phone'] ?? $staff->phone;
        $staff->address = $validated['address'] ?? $staff->address;
        $staff->role = $validated['role'];

        // Only update status if user is admin and has permission
        if (isset($validated['status']) && $currentUser->role === 'admin') {
            $staff->status = $validated['status'];
        }

        if (!empty($validated['password'])) {
            $staff->password = Hash::make($validated['password']);
        }

        $staff->save();

        return redirect()->route('staff.index')->with('success', 'Staff member updated successfully!');
    }

    public function destroy($id)
    {
        $staff = User::findOrFail($id);
        
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $staff = User::findOrFail($id);
        $currentUser = auth()->user();

        // Only admin can change staff status
        if ($currentUser->role !== 'admin') {
            return back()->with('error', 'Only Admin can change staff status!');
        }

        // Prevent changing status of other admin users (only System Owner can do this)
        // Admin can change their own status via edit form, but not via toggle button (handled in view)
        if ($staff->role === 'admin' && $currentUser->id !== 1 && $staff->id !== $currentUser->id) {
            return back()->with('error', 'Only System Owner can change Admin status!');
        }

        $staff->status = $staff->status === 'active' ? 'inactive' : 'active';
        $staff->save();

        $statusText = $staff->status === 'active' ? 'Active' : 'Inactive';
        return back()->with('success', "Staff status changed to {$statusText} successfully!");
    }
}
