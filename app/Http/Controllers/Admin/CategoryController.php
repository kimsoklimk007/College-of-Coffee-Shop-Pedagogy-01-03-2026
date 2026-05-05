<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategorySize;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    //Route to category list
    public function list()
    {
        $categories = Category::with('sizes')->paginate(5);
        return view('admin.category.list', compact('categories'));
    }

    //Route to create category page
    public function create()
    {
        return view('admin.category.create');
    }

    //Route to store category data
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'khmer_name' => ['nullable', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'sizes' => ['required', 'array'],
            'sizes.*.price_khr' => ['required', 'numeric', 'min:0'],
            'sizes.*.price_usd' => ['nullable', 'numeric', 'min:0'],
            'custom_sizes' => ['nullable', 'array'],
            'custom_sizes.*.size' => ['required_with:custom_sizes', 'string', 'max:50'],
            'custom_sizes.*.price_khr' => ['nullable', 'numeric', 'min:0'],
            'custom_sizes.*.price_usd' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            DB::beginTransaction();

            // Create category
            $category = Category::create([
                'name' => $validated['category'],
                'khmer_name' => $validated['khmer_name'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
            ]);

            // Create default sizes (Small, Medium, Large)
            foreach ($validated['sizes'] as $sizeName => $sizeData) {
                CategorySize::create([
                    'category_id' => $category->id,
                    'size' => $sizeName,
                    'price_khr' => $sizeData['price_khr'] ?? 0,
                    'price_usd' => $sizeData['price_usd'] ?? 0,
                    'is_active' => true,
                ]);
            }

            // Create custom sizes if provided
            if (!empty($validated['custom_sizes'])) {
                foreach ($validated['custom_sizes'] as $customSizeData) {
                    if (!empty($customSizeData['size'])) {
                        CategorySize::create([
                            'category_id' => $category->id,
                            'size' => $customSizeData['size'],
                            'price_khr' => $customSizeData['price_khr'] ?? 0,
                            'price_usd' => $customSizeData['price_usd'] ?? 0,
                            'is_active' => true,
                        ]);
                    }
                }
            }

            DB::commit();

            Log::info('Category created successfully', [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'sizes_count' => $category->sizes()->count(),
            ]);

            return redirect()->route('category.list')->with('alert', [
                'type' => 'success',
                'message' => __('Category created successfully with size templates'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create category', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return back()
                ->withInput()
                ->with('alert', [
                    'type' => 'error',
                    'message' => __('Failed to create category. Please try again.'),
                ]);
        }
    }

    //Route to edit category page
    public function edit($id)
    {
        // dd($id);

        $data = Category::where('id', $id)->first();

        // dd($data);

        return view('admin.category.edit', compact('data'));

    }

    //Route to update category data by id
    public function update(Request $request)
    {
        // dd($request->all());
        $validator = $request->validate([
            'category' => ['required', 'unique:categories,name,' . $request->categoryID],
            'khmer_name' => ['nullable', 'string', 'max:100'],
        ]);

        Category::where('id', $request->categoryID)->update([
            'name' => $validator['category'],
            'khmer_name' => $validator['khmer_name'] ?? null,
        ]);

        return redirect()->route('category.list')->with('alert',
            [
                'type'    => 'success',
                'message' => 'Updated Category',
            ]);
    }

    //Route to delete category data by id by checking in products table
    public function delete($id)
    {
        $productsExist = Product::where('category_id', $id)->exists();

        if ($productsExist) {
            return back()->with('error', 'This category still has products. Remove them first to delete the category');
        }

        Category::where('id', $id)->delete();

        return back()->with('success', 'Category deleted successfully');
    }

}
