<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Attendance::with(['employee', 'shift']);

        if ($request->date) {
            $query->whereDate('attendance_date', $request->date);
        } else {
            $query->whereDate('attendance_date', today());
        }

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('check_in_time', 'desc')->paginate(30);
        $employees = Employee::where('status', 'Active')->orderBy('first_name')->get();

        return view('admin.attendance.index', compact('attendances', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'Active')->orderBy('first_name')->get();
        return view('admin.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in_time' => 'required',
            'check_out_time' => 'nullable|after:check_in_time',
            'status' => 'required|in:Present,Late,Absent,On Leave',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $employee = Employee::findOrFail($request->employee_id);
            $shift = $employee->shift;

            $checkIn = Carbon::parse($request->check_in_time);
            $status = $request->status;

            if ($shift && $status === 'Present') {
                $shiftStart = Carbon::parse($shift->start_time);
                $lateThreshold = $shift->late_threshold ?? 15;

                if ($checkIn->diffInMinutes($shiftStart) > $lateThreshold) {
                    $status = 'Late';
                }
            }

            Attendance::create([
                'employee_id' => $request->employee_id,
                'shift_id' => $employee->shift_id,
                'attendance_date' => $request->attendance_date,
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'status' => $status,
                'notes' => $request->notes,
            ]);

            return redirect()->route('attendance.index')
                ->with('success', 'Attendance record created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating attendance: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $attendance = Attendance::with(['employee', 'shift'])->findOrFail($id);
        return view('admin.attendance.show', compact('attendance'));
    }

    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        $employees = Employee::where('status', 'Active')->orderBy('first_name')->get();
        return view('admin.attendance.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in_time' => 'required',
            'check_out_time' => 'nullable|after:check_in_time',
            'status' => 'required|in:Present,Late,Absent,On Leave',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $attendance->update($request->all());

            return redirect()->route('attendance.index')
                ->with('success', 'Attendance record updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating attendance: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);

        try {
            $attendance->delete();

            return redirect()->route('attendance.index')
                ->with('success', 'Attendance record deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting attendance: ' . $e->getMessage());
        }
    }

    public function qrScan(Request $request)
    {
        $employeeId = $request->employee_id;

        if (!$employeeId) {
            return response()->json(['error' => 'Invalid QR code'], 400);
        }

        $employee = Employee::where('employee_id', $employeeId)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        if ($employee->status !== 'Active') {
            return response()->json(['error' => 'Employee is not active'], 400);
        }

        $today = today();
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existingAttendance) {
            if ($existingAttendance->check_out_time) {
                return response()->json(['error' => 'Already checked out today'], 400);
            }

            $existingAttendance->update([
                'check_out_time' => now()->format('H:i:s'),
            ]);

            return response()->json([
                'message' => 'Check-out successful',
                'employee' => $employee->full_name,
                'type' => 'check_out'
            ]);
        }

        $shift = $employee->shift;
        $checkInTime = now();
        $status = 'Present';

        if ($shift) {
            $shiftStart = Carbon::parse($shift->start_time);
            $lateThreshold = $shift->late_threshold ?? 15;

            if ($checkInTime->diffInMinutes($shiftStart) > $lateThreshold) {
                $status = 'Late';
            }
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'shift_id' => $employee->shift_id,
            'attendance_date' => $today,
            'check_in_time' => $checkInTime->format('H:i:s'),
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Check-in successful',
            'employee' => $employee->full_name,
            'status' => $status,
            'type' => 'check_in'
        ]);
    }

    public function report(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        $attendances = Attendance::with('employee')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $summary = $attendances->groupBy('employee_id')->map(function ($records) {
            return [
                'total_present' => $records->whereIn('status', ['Present', 'Late'])->count(),
                'total_late' => $records->where('status', 'Late')->count(),
                'total_absent' => $records->where('status', 'Absent')->count(),
                'total_leave' => $records->where('status', 'On Leave')->count(),
            ];
        });

        return view('admin.attendance.report', compact('attendances', 'summary', 'startDate', 'endDate'));
    }
}
