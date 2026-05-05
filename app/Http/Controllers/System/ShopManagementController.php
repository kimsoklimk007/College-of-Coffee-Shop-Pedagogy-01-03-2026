<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShopManagementController extends Controller
{
    /**
     * Display list of all shops
     */
    public function index()
    {
        $shops = Shop::with('owner')
            ->when(request('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $admins = User::where('role', 'admin')->where('status', 'Active')->get();

        return view('system.shops', compact('shops', 'admins'));
    }

    /**
     * Show create shop form
     */
    public function create()
    {
        $admins = User::where('role', 'admin')
            ->where('status', 'Active')
            ->whereNull('shop_id')
            ->get();

        return view('system.shops-create', compact('admins'));
    }

    /**
     * Store new shop
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:shops,code|max:50',
            'owner_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = 'SHOP-' . Str::upper(Str::random(6));
        }

        $shop = Shop::create($validated);

        // If owner is assigned, update user's shop_id
        if (!empty($validated['owner_id'])) {
            User::where('id', $validated['owner_id'])->update(['shop_id' => $shop->id]);
        }

        // Log activity
        ActivityLog::log('create_shop', 'system', "Created new shop: {$shop->name}", $shop);

        return redirect()->route('system.shops')->with('alert', [
            'type' => 'success',
            'message' => "Shop {$shop->name} created successfully!",
        ]);
    }

    /**
     * Show edit shop form
     */
    public function edit(Shop $shop)
    {
        $admins = User::where('role', 'admin')
            ->where('status', 'Active')
            ->where(function ($query) use ($shop) {
                $query->whereNull('shop_id')
                    ->orWhere('shop_id', $shop->id);
            })
            ->get();

        return view('system.shops-edit', compact('shop', 'admins'));
    }

    /**
     * Update shop
     */
    public function update(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:shops,code,' . $shop->id . '|max:50',
            'owner_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        // Handle owner change
        if ($shop->owner_id !== $validated['owner_id']) {
            // Remove shop from old owner
            if ($shop->owner_id) {
                User::where('id', $shop->owner_id)->update(['shop_id' => null]);
            }
            // Assign shop to new owner
            if ($validated['owner_id']) {
                User::where('id', $validated['owner_id'])->update(['shop_id' => $shop->id]);
            }
        }

        $shop->update($validated);

        // Log activity
        ActivityLog::log('update_shop', 'system', "Updated shop: {$shop->name}", $shop);

        return redirect()->route('system.shops')->with('alert', [
            'type' => 'success',
            'message' => "Shop {$shop->name} updated successfully!",
        ]);
    }

    /**
     * Toggle shop status
     */
    public function toggleStatus(Shop $shop)
    {
        $shop->status = $shop->status === 'Active' ? 'Suspended' : 'Active';
        $shop->save();

        // Log activity
        ActivityLog::log('toggle_shop_status', 'system', "Changed {$shop->name} status to {$shop->status}", $shop);

        return back()->with('alert', [
            'type' => 'success',
            'message' => "Shop {$shop->name} is now {$shop->status}",
        ]);
    }

    /**
     * Show assign admin form
     */
    public function assignAdminForm(Shop $shop)
    {
        $availableAdmins = User::where('role', 'admin')
            ->where('status', 'Active')
            ->where(function ($query) use ($shop) {
                $query->whereNull('shop_id')
                    ->orWhere('shop_id', $shop->id);
            })
            ->get();

        return view('system.shops-assign-admin', compact('shop', 'availableAdmins'));
    }

    /**
     * Assign admin to shop
     */
    public function assignAdmin(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:users,id',
        ]);

        // Remove shop from old owner
        if ($shop->owner_id) {
            User::where('id', $shop->owner_id)->update(['shop_id' => null]);
        }

        // Assign new owner
        $shop->owner_id = $validated['owner_id'];
        $shop->save();

        User::where('id', $validated['owner_id'])->update(['shop_id' => $shop->id]);

        // Log activity
        ActivityLog::log('assign_admin', 'system', "Assigned admin to shop: {$shop->name}", $shop);

        return redirect()->route('system.shops')->with('alert', [
            'type' => 'success',
            'message' => "Admin assigned to {$shop->name} successfully!",
        ]);
    }

    /**
     * Delete shop
     */
    public function destroy(Shop $shop)
    {
        // Remove shop_id from all associated users
        User::where('shop_id', $shop->id)->update(['shop_id' => null]);

        $name = $shop->name;
        $shop->delete();

        // Log activity
        ActivityLog::log('delete_shop', 'system', "Deleted shop: {$name}");

        return back()->with('alert', [
            'type' => 'success',
            'message' => "Shop {$name} deleted successfully!",
        ]);
    }
}
