<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ingredient;
use App\Models\Category;
use App\Models\Purchase_Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $ingredients = Ingredient::with('category')
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->stock_status, function ($query) use ($request) {
                if ($request->stock_status === 'low') {
                    $query->whereRaw('stock_quantity <= low_stock_threshold');
                } elseif ($request->stock_status === 'out') {
                    $query->where('stock_quantity', '<=', 0);
                } elseif ($request->stock_status === 'in_stock') {
                    $query->whereRaw('stock_quantity > low_stock_threshold');
                }
            })
            ->paginate(10);

        $categories = Category::all();
        
        return view('admin.inventory.index', compact('ingredients', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.inventory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'low_stock_threshold' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        Ingredient::create($validated);
        return redirect()->route('inventory.index')->with('success', 'Ingredient added successfully!');
    }

    public function edit($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $categories = Category::all();
        return view('admin.inventory.edit', compact('ingredient', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'low_stock_threshold' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $ingredient = Ingredient::findOrFail($id);
        $ingredient->update($validated);
        return redirect()->route('inventory.index')->with('success', 'Ingredient updated successfully!');
    }

    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete();
        return redirect()->route('inventory.index')->with('success', 'Ingredient deleted successfully!');
    }

    public function adjustStock(Request $request, $id)
    {
        $validated = $request->validate([
            'adjustment' => 'required|numeric',
            'type' => 'required|in:add,subtract',
            'note' => 'nullable|string|max:255',
        ]);

        $ingredient = Ingredient::findOrFail($id);
        
        if ($validated['type'] === 'add') {
            $ingredient->stock_quantity += $validated['adjustment'];
        } else {
            $ingredient->stock_quantity -= $validated['adjustment'];
            if ($ingredient->stock_quantity < 0) {
                $ingredient->stock_quantity = 0;
            }
        }
        
        $ingredient->save();
        
        return redirect()->back()->with('success', 'Stock adjusted successfully!');
    }

    public function lowStock()
    {
        $ingredients = Ingredient::whereRaw('stock_quantity <= low_stock_threshold')
            ->with('category')
            ->get();
            
        return view('admin.inventory.low_stock', compact('ingredients'));
    }

    public function stockHistory($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $purchaseItems = Purchase_Item::where('ingredient_id', $id)
            ->with('purchase')
            ->orderBy('id', 'desc')
            ->paginate(20);
            
        return view('admin.inventory.stock_history', compact('ingredient', 'purchaseItems'));
    }
}
