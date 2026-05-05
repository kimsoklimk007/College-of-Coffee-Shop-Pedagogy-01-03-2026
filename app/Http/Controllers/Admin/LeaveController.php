<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Leave::with(['employee', 'approver']);

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->leave_type) {
            $query->where('leave_type', $request->leave_type);
        }

        if ($request->start_date) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $leaves = $query->latest()->paginate(30);
        $employees = Employee::where('status', 'Active')->orderBy('first_name')->get();

        return view('admin.leave.index', compact('leaves', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'Active')->orderBy('first_name')->get();
        return view('admin.leave.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|in:Annual,Sick,Personal,Unpaid,Other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;

            Leave::create([
                'employee_id' => $request->employee_id,
                'leave_type' => $request->leave_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_days' => $totalDays,
                'reason' => $request->reason,
                'status' => 'Pending',
                'notes' => $request->notes,
                'applied_by' => auth()->id(),
            ]);

            return redirect()->route('leave.index')
                ->with('success', 'Leave request submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating leave: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $leave = Leave::with(['employee', 'approver'])->findOrFail($id);
        $leaveBalances = LeaveBalance::getEmployeeBalances($leave->employee_id);
        return view('admin.leave.show', compact('leave', 'leaveBalances'));
    }

    public function edit($id)
    {
        $leave = Leave::findOrFail($id);
        $employees = Employee::where('status', 'Active')->orderBy('first_name')->get();
        return view('admin.leave.edit', compact('leave', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|in:Annual,Sick,Personal,Unpaid,Other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;

            $leave->update([
                'employee_id' => $request->employee_id,
                'leave_type' => $request->leave_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_days' => $totalDays,
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            return redirect()->route('leave.index')
                ->with('success', 'Leave request updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating leave: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);

        try {
            $leave->delete();

            return redirect()->route('leave.index')
                ->with('success', 'Leave request deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting leave: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $leave = Leave::findOrFail($id);

        try {
            $leave->update([
                'status' => 'Approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $leaveBalances = LeaveBalance::where('employee_id', $leave->employee_id)
                ->where('leave_type', $leave->leave_type)
                ->first();

            if ($leaveBalances) {
                $leaveBalances->decrement('used_days', $leave->total_days);
            }

            return redirect()->back()
                ->with('success', 'Leave request approved.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error approving leave: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $leave = Leave::findOrFail($id);

        try {
            $leave->update([
                'status' => 'Rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Leave request rejected.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error rejecting leave: ' . $e->getMessage());
        }
    }

    public function balances(Request $request)
    {
        $employeeId = $request->employee_id;

        if ($employeeId) {
            $balances = LeaveBalance::where('employee_id', $employeeId)->get();
            $employee = Employee::findOrFail($employeeId);
            return view('admin.leave.balances', compact('balances', 'employee'));
        }

        $employees = Employee::where('status', 'Active')->orderBy('first_name')->get();
        return view('admin.leave.balances', compact('employees'));
    }
}
