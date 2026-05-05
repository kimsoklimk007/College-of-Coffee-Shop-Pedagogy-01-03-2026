<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Order;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SystemDashboardController extends Controller
{
    /**
     * Display the system dashboard for Super Admin
     */
    public function index()
    {
        $data = [
            'totalUsers' => User::count(),
            'totalShops' => Shop::count(),
            'activeShops' => Shop::where('status', 'Active')->count(),
            'totalOrders' => Order::count(),
            'todayOrders' => Order::whereDate('created_at', now())->count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentShops' => Shop::latest()->take(5)->get(),
            'recentActivity' => ActivityLog::latest()->take(10)->get(),
            'shopsByStatus' => Shop::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get(),
            'usersByRole' => User::select('role', DB::raw('COUNT(*) as count'))
                ->groupBy('role')
                ->get(),
        ];

        return view('system.dashboard', $data);
    }

    /**
     * Display system users management page
     */
    public function users()
    {
        $users = User::with('shop')->latest()->paginate(20);
        return view('system.users', compact('users'));
    }

    /**
     * Display shops management page
     */
    public function shops()
    {
        $shops = Shop::with('owner')->latest()->paginate(20);
        return view('system.shops', compact('shops'));
    }

    /**
     * Display system activity logs
     */
    public function activityLogs()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(50);
        return view('system.activity-logs', compact('logs'));
    }

    /**
     * Toggle user status (block/unblock)
     */
    public function toggleUserStatus(User $user)
    {
        // Prevent blocking yourself
        if ($user->id === Auth::id()) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'You cannot block yourself!',
            ]);
        }

        $user->status = $user->status === 'Active' ? 'Inactive' : 'Active';
        $user->save();

        // Log the action
        ActivityLog::log('toggle_user_status', 'system', "Changed user {$user->name} status to {$user->status}", $user);

        return back()->with('alert', [
            'type' => 'success',
            'message' => "User status updated to {$user->status}",
        ]);
    }

    /**
     * Reset user password
     */
    public function resetUserPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = bcrypt($request->password);
        $user->save();

        // Log the action
        ActivityLog::log('reset_password', 'system', "Reset password for user: {$user->name}", $user);

        return back()->with('alert', [
            'type' => 'success',
            'message' => "Password reset successfully for {$user->name}",
        ]);
    }

    /**
     * Toggle shop status (suspend/activate)
     */
    public function toggleShopStatus(Shop $shop)
    {
        $shop->status = $shop->status === 'Active' ? 'Suspended' : 'Active';
        $shop->save();

        // Log the action
        ActivityLog::log('toggle_shop_status', 'system', "Changed shop {$shop->name} status to {$shop->status}", $shop);

        return back()->with('alert', [
            'type' => 'success',
            'message' => "Shop status updated to {$shop->status}",
        ]);
    }

    /**
     * Display system-wide reports
     */
    public function reports()
    {
        $data = [
            'totalRevenue' => Order::sum('totalprice'),
            'monthlyRevenue' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('totalprice'),
            'ordersByMonth' => Order::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(totalprice) as revenue')
                )
                ->whereYear('created_at', now()->year)
                ->groupBy('year', 'month')
                ->orderBy('month')
                ->get(),
            'topShops' => Shop::withCount('orders')
                ->withSum('orders', 'totalprice')
                ->orderByDesc('orders_sum_totalprice')
                ->take(5)
                ->get(),
        ];

        return view('system.reports', $data);
    }
}
