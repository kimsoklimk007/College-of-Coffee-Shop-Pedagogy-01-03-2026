<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = EmployeeRole::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $roles = $query->orderBy('name')->paginate(15);

        return view('admin.role.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.role.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:employee_roles,name',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['permissions'] = json_encode($request->permissions ?? []);

            EmployeeRole::create($data);

            return redirect()->route('role.index')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating role: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $role = EmployeeRole::with('employees')->findOrFail($id);
        return view('admin.role.show', compact('role'));
    }

    public function edit($id)
    {
        $role = EmployeeRole::findOrFail($id);
        return view('admin.role.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $role = EmployeeRole::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:employee_roles,name,' . $id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['permissions'] = json_encode($request->permissions ?? []);

            $role->update($data);

            return redirect()->route('role.index')
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating role: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $role = EmployeeRole::findOrFail($id);

        try {
            if ($role->employees()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete role with assigned employees.');
            }

            $role->delete();

            return redirect()->route('role.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $role = EmployeeRole::findOrFail($id);

        try {
            $role->status = $role->status === 'Active' ? 'Inactive' : 'Active';
            $role->save();

            return redirect()->back()
                ->with('success', 'Role status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }
}
