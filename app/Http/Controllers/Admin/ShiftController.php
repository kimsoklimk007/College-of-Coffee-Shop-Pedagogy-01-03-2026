<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Shift::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $shifts = $query->orderBy('start_time')->paginate(15);

        return view('admin.shift.index', compact('shifts'));
    }

    public function create()
    {
        return view('admin.shift.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:shifts,name',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'late_threshold' => 'nullable|integer|min:0',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->status === 'Active';
            unset($data['status']);

            Shift::create($data);

            return redirect()->route('shift.index')
                ->with('success', 'Shift created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating shift: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $shift = Shift::with('employees')->findOrFail($id);
        return view('admin.shift.show', compact('shift'));
    }

    public function edit($id)
    {
        $shift = Shift::findOrFail($id);
        return view('admin.shift.edit', compact('shift'));
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:shifts,name,' . $id,
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'late_threshold' => 'nullable|integer|min:0',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->status === 'Active';
            unset($data['status']);

            $shift->update($data);

            return redirect()->route('shift.index')
                ->with('success', 'Shift updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating shift: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);

        try {
            $shift->delete();

            return redirect()->route('shift.index')
                ->with('success', 'Shift deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting shift: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $shift = Shift::findOrFail($id);

        try {
            $shift->status = $shift->status === 'Active' ? 'Inactive' : 'Active';
            $shift->save();

            return redirect()->back()
                ->with('success', 'Shift status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }
}
