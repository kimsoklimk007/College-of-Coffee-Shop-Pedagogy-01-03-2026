<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategorySize;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    //
    public function prodlist()
    {
        $products = Product::select(
            'products.*',
            DB::raw('products.qty - COALESCE(SUM(orders.quantity), 0) as available_stock')
        )
            ->leftJoin('orders', function ($join) {
                $join->on('orders.product_id', '=', 'products.id')
                    ->where('orders.status', 2);
            })
            ->when(request('searchKey'), function ($query) {
                $query->where('products.name', 'like', '%' . request('searchKey') . '%');
            })
            ->groupBy(
                'products.name',
                'products.qty',
                'products.category_id',
                'products.description',
                'products.image',
                'products.created_at',
                'products.updated_at',
                'products.id'
            )
            ->orderBy('products.id')
            ->paginate(8);

        // For each product, manually load sizes
        foreach ($products as $product) {
            $product->sizes = DB::table('product_sizes')
                ->where('product_id', $product->id)
                ->get(['size', 'price', 'price_khr', 'price_usd', 'currency']);
        }
        // dd($products->all());

        return view('admin.product.prodlist', compact('products'));
    }

    public function prodcreate()
    {
        $categories = Category::get();
        return view('admin.product.prodcreate', compact('categories'));
    }

    public function prodstore(Request $request)
    {
        // dd($request->all());

        $this->validationCheck($request, "create");

        $data = $this->requestProductData($request);

        if ($request->hasFile('image')) {
            $fileName = uniqid() . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path() . '/productImages/', $fileName);
            $data['image'] = $fileName;
        }

        try {
            DB::beginTransaction();

            // Create product
            $product = Product::create($data);

            // Process sizes and prices
            $sizes = $request->sizes;
            $pricesKhr = $request->prices_khr;
            $pricesUsd = $request->prices_usd;
            $currencies = $request->currencies ?? [];
            $exchangeRate = 4100;

            $productSizes = [];

            foreach ($sizes as $index => $size) {
                $khrValue = !empty($pricesKhr[$index]) ? $pricesKhr[$index] : null;
                $usdValue = !empty($pricesUsd[$index]) ? $pricesUsd[$index] : null;
                $currency = $currencies[$index] ?? 'KHR';

                // Auto-convert if only one currency is provided
                if ($khrValue && !$usdValue) {
                    $usdValue = round($khrValue / $exchangeRate, 2);
                } elseif ($usdValue && !$khrValue) {
                    $khrValue = round($usdValue * $exchangeRate);
                }

                $productSizes[] = [
                    'product_id' => $product->id,
                    'size'       => $size,
                    'price'      => $khrValue ?? $usdValue,
                    'price_khr'  => $khrValue,
                    'price_usd'  => $usdValue,
                    'currency'   => $currency,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert all sizes at once
            if (!empty($productSizes)) {
                ProductSize::insert($productSizes);
            }

            DB::commit();

            return redirect()->route('product.prodlist')->with('alert', [
                'type'    => 'success',
                'message' => 'Product and sizes added successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('alert', [
                'type'    => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    private function validationCheck($request, $action)
    {
        $rules = [
            'name'          => ['required', 'unique:products,name,' . $request->productId],
            'name_kh'       => 'nullable|string|max:100',
            'stock'         => ['required', 'integer', 'min:0'],
            'description'   => 'required',
            'category_name' => 'required|exists:categories,id',
            'sizes'         => 'required|array|min:1',
            'sizes.*'       => 'required|string|in:S,M,L,XXL,XXX,LLX,XLL,ALL',
            'prices_khr'    => 'required|array',
            'prices_khr.*'  => 'nullable|numeric|min:0',
            'prices_usd'    => 'required|array',
            'prices_usd.*'  => 'nullable|numeric|min:0',
            'currencies'    => 'nullable|array',
            'currencies.*'  => 'nullable|string|in:KHR,USD',
        ];

        $rules['image'] = $action == 'create' ? ['required', 'mimes:png,jpg,jpeg,webp,svg,gif,bmp'] : ['mimes:png,jpg,jpeg,webp,svg,gif,bmp'];

        $validator = $request->validate($rules);
    }

    private function requestProductData($request)
    {
        return [
            'name'        => $request->name,
            'name_kh'     => $request->name_kh,
            'category_id' => $request->category_name,
            'description' => $request->description,
            'qty'         => $request->stock,
        ];
    }

    public function prodedit($id)
    {
        // dd($id);
        $products = Product::select('products.id',
            'products.name',
            'products.name_kh',
            'products.image',
            'products.qty',
            'products.description',
            'products.category_id',
            'product_sizes.size',
            'product_sizes.price',
            'categories.name as category_name')
            ->leftJoin('categories', 'products.category_id', 'categories.id')
            ->leftJoin('product_sizes', 'products.id', 'product_sizes.product_id')
            ->where('products.id', $id)
            ->get();

        $categories = Category::get();

        // dd($products->toArray());

        return view('admin.product.prodedit', compact('products', 'categories'));

    }

    //update Product
    public function produpdate(Request $request)
    {
        // dd($request->all());
        $this->validationCheck($request, "update");

        $data = $this->requestProductData($request);

        // Handle image upload
        if ($request->hasFile('image')) {
            if (file_exists(public_path('productImages/' . $request->oldImage))) {
                unlink(public_path('productImages/' . $request->oldImage));
            }

            $fileName = uniqid() . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('productImages'), $fileName);
            $image = $fileName;
        } else {
            $image = $request->oldImage;
        }

        // Prepare data for Product table
        $productData = [
            'name'        => $data['name'],
            'name_kh'     => $data['name_kh'],
            'category_id' => $data['category_id'],
            'description' => $data['description'],
            'qty'         => $data['qty'],
            'image'       => $image,
        ];

        try {
            DB::beginTransaction();

            // Update Product table
            Product::where('id', $request->productId)->update($productData);

            // Get existing sizes for this product
            $existingSizes = ProductSize::where('product_id', $request->productId)
                ->pluck('size')
                ->toArray();

            // Process sizes and prices
            $sizes = $request->sizes;
            $pricesKhr = $request->prices_khr;
            $pricesUsd = $request->prices_usd;
            $currencies = $request->currencies ?? [];
            $exchangeRate = 4100;

            $sizesToAdd = [];
            $sizesToUpdate = [];

            foreach ($sizes as $index => $size) {
                $khrValue = !empty($pricesKhr[$index]) ? $pricesKhr[$index] : null;
                $usdValue = !empty($pricesUsd[$index]) ? $pricesUsd[$index] : null;
                $currency = $currencies[$index] ?? 'KHR';

                // Auto-convert if only one currency is provided
                if ($khrValue && !$usdValue) {
                    $usdValue = round($khrValue / $exchangeRate, 2);
                } elseif ($usdValue && !$khrValue) {
                    $khrValue = round($usdValue * $exchangeRate);
                }

                $sizeData = [
                    'size'       => $size,
                    'price'      => $khrValue ?? $usdValue,
                    'price_khr'  => $khrValue,
                    'price_usd'  => $usdValue,
                    'currency'   => $currency,
                    'updated_at' => now(),
                ];

                if (in_array($size, $existingSizes)) {
                    // Update existing size
                    ProductSize::where('product_id', $request->productId)
                        ->where('size', $size)
                        ->update($sizeData);
                } else {
                    // Add new size
                    $sizeData['product_id'] = $request->productId;
                    $sizeData['created_at'] = now();
                    $sizesToAdd[] = $sizeData;
                }
            }

            // Delete sizes that are no longer selected
            $sizesToDelete = array_diff($existingSizes, $sizes);
            if (!empty($sizesToDelete)) {
                ProductSize::where('product_id', $request->productId)
                    ->whereIn('size', $sizesToDelete)
                    ->delete();
            }

            // Insert new sizes
            if (!empty($sizesToAdd)) {
                ProductSize::insert($sizesToAdd);
            }

            DB::commit();

            return redirect()->route('product.prodlist')->with('alert', [
                'type'    => 'success',
                'message' => 'Update Product Successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('alert', [
                'type'    => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    //delete Products
    public function proddelete($id)
    {
        // dd($id);
        Product::where('id', $id)->delete();
        return redirect()->route('product.prodlist');
    }
    //route to prodsize page
    public function prodsize($id)
    {
        // dd($id);
        $product    = Product::findOrFail($id);
        $categories = Category::all();

        // dd($product);
        return view('admin.product.productsize', compact('product', 'categories'));
    }

    //add product price and size
    public function prodsizestore(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'sizes'       => 'required|array',
            'sizes.*'     => 'required|string|in:S,M,L,XXL,XXX,LLX,XLL,ALL',
            'prices_khr'  => 'required|array',
            'prices_khr.*' => 'nullable|numeric|min:0',
            'prices_usd'  => 'required|array',
            'prices_usd.*' => 'nullable|numeric|min:0',
        ]);

        $sizes      = $validated['sizes'];
        $pricesKhr  = $validated['prices_khr'];
        $pricesUsd  = $validated['prices_usd'];
        $currencies = $request->currencies ?? [];
        $exchangeRate = 4100;

        $existingSizes = ProductSize::where('product_id', $product->id)
            ->pluck('size')
            ->toArray();

        $duplicates = [];
        $newSizes   = [];

        foreach ($sizes as $index => $size) {
            if (in_array($size, $existingSizes)) {
                $duplicates[] = $size;
            } else {
                $khrValue = !empty($pricesKhr[$index]) ? $pricesKhr[$index] : null;
                $usdValue = !empty($pricesUsd[$index]) ? $pricesUsd[$index] : null;

                if ($khrValue && !$usdValue) {
                    $usdValue = round($khrValue / $exchangeRate, 2);
                } elseif ($usdValue && !$khrValue) {
                    $khrValue = round($usdValue * $exchangeRate);
                }

                $currency = $currencies[$index] ?? 'KHR';

                $newSizes[] = [
                    'product_id' => $product->id,
                    'size'       => $size,
                    'price'      => $khrValue ?? $usdValue,
                    'price_khr'  => $khrValue,
                    'price_usd'  => $usdValue,
                    'currency'   => $currency,
                ];
            }
        }

        if (! empty($newSizes)) {
            ProductSize::insert($newSizes);
        }

        if (! empty($duplicates)) {
            return back()->with('alert', [
                'type'    => 'error',
                'message' => 'These sizes already exist for this product: ' . implode(', ', $duplicates),
            ]);
        }

        return redirect()->route('product.prodlist')->with('alert', [
            'type'    => 'success',
            'message' => 'Product sizes added successfully.',
        ]);
    }

    //Discount Products
    public function discountPage()
    {
        $product = Product::get();
        return view('admin.product.discount', compact('product'));
    }

                                                    //Add Discount table
    public function adddiscount(Request $request)
    { // dd($request->all());
                                                        // Basic validation for common fields
        $validated = $request->validate([
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after_or_equal:start_date',
        ]);

        // Extra validation if not applying to all products
        if (! $request->has('apply_to_all')) {
            $request->validate([
                'product_id' => 'required|exists:products,id',
            ]);
        }

        // Prepare data for updateOrCreate
        $discountData = [
            'discount_percentage' => $validated['discount_percentage'],
            'start_date'          => $validated['start_date'],
            'end_date'            => $validated['end_date'],
        ];

        if ($request->has('apply_to_all')) {
            // Apply to all products
            $products = Product::all();
        } else {
            // Apply to a specific product
            $products = Product::where('id', $request->input('product_id'))->get();
        }

        // Save discount for each applicable product
        foreach ($products as $product) {
            Discount::updateOrCreate(
                ['product_id' => $product->id],
                $discountData
            );
        }
        return redirect()->route('discountPage')->with('alert',
            [
                'type'    => 'success',
                'message' => 'Discount applied successfully!',
            ]);

    }

    // Get category sizes with default prices
    public function getCategorySizes($categoryId)
    {
        try {
            $categorySizes = CategorySize::where('category_id', $categoryId)
                ->where('is_active', true)
                ->orderBy('id')
                ->get(['size', 'price_khr', 'price_usd']);

            $sizeLabels = [
                'S' => 'Small',
                'M' => 'Medium',
                'L' => 'Large',
                'XXL' => 'Extra Extra Large',
                'XXX' => 'Triple Extra Large',
                'LLX' => 'Double Large XL',
                'XLL' => 'Extra Large Large',
                'ALL' => 'All Sizes'
            ];

            // Add labels to sizes
            $sizesWithLabels = $categorySizes->map(function($size) use ($sizeLabels) {
                return [
                    'size' => $size->size,
                    'price_khr' => $size->price_khr,
                    'price_usd' => $size->price_usd,
                    'label' => $sizeLabels[$size->size] ?? $size->size
                ];
            });

            return response()->json([
                'success' => true,
                'sizes' => $sizesWithLabels
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading category sizes: ' . $e->getMessage()
            ], 500);
        }
    }

}
