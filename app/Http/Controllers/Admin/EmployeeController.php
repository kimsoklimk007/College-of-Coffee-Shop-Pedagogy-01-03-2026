<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Shift;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\EmployeeDocument;
use App\Models\ActivityLog;
use App\Models\User;

class EmployeeController extends Controller
{
    
    public function settings()
    {
        // Get employee statistics in single query
        $employeeStats = Employee::selectRaw('
            count(*) as total,
            sum(case when status = "Active" then 1 else 0 end) as active,
            sum(case when status = "Inactive" then 1 else 0 end) as inactive,
            sum(case when status = "Resigned" then 1 else 0 end) as resigned
        ')->first();

        $totalEmployees = $employeeStats->total;
        $activeEmployees = $employeeStats->active;
        $inactiveEmployees = $employeeStats->inactive;
        $resignedEmployees = $employeeStats->resigned;

        // Get today's attendance statistics using database aggregation
        $todayDate = now()->toDateString();
        $presentToday = Attendance::whereDate('attendance_date', $todayDate)
            ->whereIn('status', ['Present', 'Late'])
            ->count();
        $lateToday = Attendance::whereDate('attendance_date', $todayDate)
            ->where('status', 'Late')
            ->count();
        $absentToday = $totalEmployees - $presentToday;
        
        // Get recent payroll summary
        $monthlyPayroll = Payroll::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->sum('net_salary');
        
        return view('admin.employee.settings', compact(
            'totalEmployees',
            'activeEmployees', 
            'inactiveEmployees',
            'resignedEmployees',
            'presentToday',
            'lateToday',
            'absentToday',
            'monthlyPayroll'
        ));
    }

    public function index(Request $request)
    {
        $query = Employee::with(['role', 'shift']);

        // Search functionality
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by role
        if ($request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by shift
        if ($request->shift_id) {
            $query->where('shift_id', $request->shift_id);
        }

        $employees = $query->latest()->paginate(15);
        $roles = EmployeeRole::active()->get();
        $shifts = Shift::active()->get();

        return view('admin.employee.index', compact('employees', 'roles', 'shifts'));
    }

    public function create()
    {
        $roles = EmployeeRole::active()->get();
        $shifts = Shift::active()->get();
        
        return view('admin.employee.create', compact('roles', 'shifts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'first_name_kh' => 'nullable|string|max:255',
            'last_name_kh' => 'nullable|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:100',
            'id_card_number' => 'nullable|string|unique:employees,id_card_number',
            'passport_number' => 'nullable|string|unique:employees,passport_number',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email',
            'address' => 'required|string',
            'address_kh' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'hire_date' => 'required|date|before_or_equal:today',
            'base_salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'role_id' => 'required|exists:employee_roles,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'employment_type' => 'required|in:Full-time,Part-time,Contract,Intern',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_card_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contract_document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $employeeData = $request->all();
            
            // Handle file uploads
            if ($request->hasFile('profile_photo')) {
                $profilePhoto = $request->file('profile_photo');
                $profilePhotoPath = $profilePhoto->store('employee/photos', 'public');
                $employeeData['profile_photo'] = $profilePhotoPath;
            }

            if ($request->hasFile('id_card_photo')) {
                $idCardPhoto = $request->file('id_card_photo');
                $idCardPhotoPath = $idCardPhoto->store('employee/id_cards', 'public');
                $employeeData['id_card_photo'] = $idCardPhotoPath;
            }

            if ($request->hasFile('contract_document')) {
                $contractDoc = $request->file('contract_document');
                $contractDocPath = $contractDoc->store('employee/contracts', 'public');
                $employeeData['contract_document'] = $contractDocPath;
            }

            $employee = Employee::create($employeeData);

            // Initialize leave balances
            LeaveBalance::initializeBalances($employee);

            // Generate QR code
            $employee->generateQrCode();
            $employee->save();

            // Log activity
            ActivityLog::logEmployeeAction('create', "Created new employee: {$employee->full_name}", $employee);

            return redirect()->route('employee.index')
                ->with('success', 'Employee created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $employee = Employee::with([
            'role', 
            'shift', 
            'attendance' => function ($query) {
                $query->latest()->take(30);
            },
            'leaves' => function ($query) {
                $query->latest()->take(10);
            },
            'documents' => function ($query) {
                $query->latest()->take(10);
            },
            'payroll' => function ($query) {
                $query->latest()->take(12);
            }
        ])->findOrFail($id);

        // Get attendance statistics
        $attendanceStats = [
            'this_month' => $employee->attendance()
                ->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year)
                ->get(),
            'last_month' => $employee->attendance()
                ->whereMonth('attendance_date', now()->subMonth()->month)
                ->whereYear('attendance_date', now()->subMonth()->year)
                ->get(),
        ];

        // Get leave balances
        $leaveBalances = LeaveBalance::getEmployeeBalances($employee->id);

        return view('admin.employee.show', compact('employee', 'attendanceStats', 'leaveBalances'));
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $roles = EmployeeRole::active()->get();
        $shifts = Shift::active()->get();
        
        return view('admin.employee.edit', compact('employee', 'roles', 'shifts'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'first_name_kh' => 'nullable|string|max:255',
            'last_name_kh' => 'nullable|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:100',
            'id_card_number' => 'nullable|string|unique:employees,id_card_number,' . $id,
            'passport_number' => 'nullable|string|unique:employees,passport_number,' . $id,
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email,' . $id,
            'address' => 'required|string',
            'address_kh' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'base_salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'role_id' => 'required|exists:employee_roles,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'employment_type' => 'required|in:Full-time,Part-time,Contract,Intern',
            'status' => 'required|in:Active,Inactive,On Leave,Resigned,Terminated',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_card_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contract_document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $oldValues = $employee->toArray();
            $employeeData = $request->all();

            // Handle file uploads
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($employee->profile_photo) {
                    Storage::disk('public')->delete($employee->profile_photo);
                }
                
                $profilePhoto = $request->file('profile_photo');
                $profilePhotoPath = $profilePhoto->store('employee/photos', 'public');
                $employeeData['profile_photo'] = $profilePhotoPath;
            }

            if ($request->hasFile('id_card_photo')) {
                // Delete old photo if exists
                if ($employee->id_card_photo) {
                    Storage::disk('public')->delete($employee->id_card_photo);
                }
                
                $idCardPhoto = $request->file('id_card_photo');
                $idCardPhotoPath = $idCardPhoto->store('employee/id_cards', 'public');
                $employeeData['id_card_photo'] = $idCardPhotoPath;
            }

            if ($request->hasFile('contract_document')) {
                // Delete old document if exists
                if ($employee->contract_document) {
                    Storage::disk('public')->delete($employee->contract_document);
                }
                
                $contractDoc = $request->file('contract_document');
                $contractDocPath = $contractDoc->store('employee/contracts', 'public');
                $employeeData['contract_document'] = $contractDocPath;
            }

            // Handle end date for resigned/terminated employees
            if (in_array($request->status, ['Resigned', 'Terminated']) && !$request->end_date) {
                $employeeData['end_date'] = now();
            } elseif ($request->status === 'Active' && $request->end_date) {
                $employeeData['end_date'] = null;
            }

            $employee->update($employeeData);

            // Log activity
            ActivityLog::logEmployeeAction('update', "Updated employee: {$employee->full_name}", $employee, $oldValues, $employeeData);

            return redirect()->route('employee.show', $employee->id)
                ->with('success', 'Employee updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        try {
            $employeeName = $employee->full_name;
            
            // Soft delete
            $employee->delete();

            // Log activity
            ActivityLog::logEmployeeAction('delete', "Deleted employee: {$employeeName}", $employee);

            return redirect()->route('employee.index')
                ->with('success', 'Employee deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }

    public function generateQrCode($id)
    {
        $employee = Employee::findOrFail($id);

        try {
            $qrPath = $employee->generateQrCode();
            $employee->save();

            // Log activity
            ActivityLog::logEmployeeAction('update', "Generated QR code for employee: {$employee->full_name}", $employee);

            return redirect()->route('employee.show', $employee->id)
                ->with('success', 'QR code generated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error generating QR code: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $employee = Employee::findOrFail($id);

        try {
            $oldStatus = $employee->status;
            
            if ($employee->status === 'Active') {
                $employee->status = 'Inactive';
            } else {
                $employee->status = 'Active';
            }

            $employee->save();

            // Log activity
            ActivityLog::logEmployeeAction('update', "Changed status from {$oldStatus} to {$employee->status} for employee: {$employee->full_name}", $employee);

            return redirect()->back()
                ->with('success', 'Employee status updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        // Implementation for employee data export
        // This would generate Excel/CSV export
        
        return redirect()->back()
            ->with('info', 'Export feature coming soon.');
    }

    public function import(Request $request)
    {
        // Implementation for employee data import
        // This would handle Excel/CSV import
        
        return redirect()->back()
            ->with('info', 'Import feature coming soon.');
    }
}
