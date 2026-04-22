<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Payroll::with(['employee', 'approver']);

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->month) {
            $query->whereMonth('payment_date', Carbon::parse($request->month)->month)
                  ->whereYear('payment_date', Carbon::parse($request->month)->year);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->orderBy('payment_date', 'desc')->paginate(30);
        $employees = Employee::where('status', 'Active')->orderBy('first_name')->get();

        return view('admin.payroll.index', compact('payrolls', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'Active')->orderBy('first_name')->get();
        return view('admin.payroll.create', compact('employees'));
    }

    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $employee = Employee::findOrFail($request->employee_id);
            $month = Carbon::parse($request->month);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
                ->get();

            $workingDays = $attendances->whereIn('status', ['Present', 'Late'])->count();
            $lateDays = $attendances->where('status', 'Late')->count();
            $absentDays = $attendances->where('status', 'Absent')->count();

            $leaves = Leave::where('employee_id', $employee->id)
                ->where('status', 'Approved')
                ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                ->get();

            $leaveDays = $leaves->sum('total_days');

            $baseSalary = $employee->base_salary;
            $dailyRate = $baseSalary / 26;
            $workedSalary = $dailyRate * $workingDays;

            $lateDeduction = $lateDays * ($dailyRate * 0.1);
            $absentDeduction = $absentDays * $dailyRate;

            $overtimeHours = 0;
            $overtimeRate = $dailyRate / 8 * 1.5;
            $overtimePay = $overtimeHours * $overtimeRate;

            $bonus = $request->bonus ?? 0;
            $allowance = $request->allowance ?? 0;

            $grossSalary = $workedSalary + $overtimePay + $bonus + $allowance;
            $totalDeduction = $lateDeduction + $absentDeduction + ($request->deduction ?? 0);

            $netSalary = $grossSalary - $totalDeduction;

            $data = [
                'employee' => $employee,
                'month' => $month->format('F Y'),
                'working_days' => $workingDays,
                'late_days' => $lateDays,
                'absent_days' => $absentDays,
                'leave_days' => $leaveDays,
                'base_salary' => $baseSalary,
                'worked_salary' => $workedSalary,
                'overtime_pay' => $overtimePay,
                'bonus' => $bonus,
                'allowance' => $allowance,
                'late_deduction' => $lateDeduction,
                'absent_deduction' => $absentDeduction,
                'other_deduction' => $request->deduction ?? 0,
                'gross_salary' => $grossSalary,
                'total_deduction' => $totalDeduction,
                'net_salary' => $netSalary,
            ];

            return view('admin.payroll.calculate', $data);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error calculating payroll: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'payment_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'working_days' => 'required|integer|min:0',
            'late_days' => 'required|integer|min:0',
            'absent_days' => 'required|integer|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'late_deduction' => 'nullable|numeric|min:0',
            'absent_deduction' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'gross_salary' => 'required|numeric|min:0',
            'net_salary' => 'required|numeric|min:0',
            'status' => 'required|in:Pending,Paid',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['overtime_hours'] = $request->overtime_hours ?? 0;
            $data['overtime_rate'] = $request->overtime_rate ?? 0;
            $data['overtime_pay'] = ($data['overtime_hours'] * $data['overtime_rate']);
            $data['bonus'] = $request->bonus ?? 0;
            $data['allowance'] = $request->allowance ?? 0;
            $data['late_deduction'] = $request->late_deduction ?? 0;
            $data['absent_deduction'] = $request->absent_deduction ?? 0;
            $data['other_deduction'] = $request->other_deduction ?? 0;
            $data['approved_by'] = auth()->id();

            Payroll::create($data);

            return redirect()->route('payroll.index')
                ->with('success', 'Payroll record created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating payroll: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $payroll = Payroll::with(['employee', 'approver'])->findOrFail($id);
        return view('admin.payroll.show', compact('payroll'));
    }

    public function edit($id)
    {
        $payroll = Payroll::findOrFail($id);
        $employees = Employee::where('status', 'Active')->orderBy('first_name')->get();
        return view('admin.payroll.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'payment_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'working_days' => 'required|integer|min:0',
            'status' => 'required|in:Pending,Paid',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $payroll->update($request->all());

            return redirect()->route('payroll.index')
                ->with('success', 'Payroll record updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating payroll: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);

        try {
            $payroll->delete();

            return redirect()->route('payroll.index')
                ->with('success', 'Payroll record deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting payroll: ' . $e->getMessage());
        }
    }

    public function report(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');

        $payrolls = Payroll::with('employee')
            ->whereMonth('payment_date', Carbon::parse($month)->month)
            ->whereYear('payment_date', Carbon::parse($month)->year)
            ->get();

        $totalGross = $payrolls->sum('gross_salary');
        $totalDeduction = $payrolls->sum('total_deduction');
        $totalNet = $payrolls->sum('net_salary');

        return view('admin.payroll.report', compact('payrolls', 'month', 'totalGross', 'totalDeduction', 'totalNet'));
    }
}
