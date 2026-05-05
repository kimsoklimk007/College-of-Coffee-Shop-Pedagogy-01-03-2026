<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Review;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Discount;
use App\Models\TaxSetting;
use Illuminate\Support\Facades\Validator;

class RouteController extends Controller
{
    // ==================== CATEGORY API ====================
    
    public function categoryList()
    {
        $categories = Category::with('sizes')->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ], 200);
    }

    public function categoryDetail($id)
    {
        $category = Category::with('products', 'sizes')->find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category retrieved successfully'
        ], 200);
    }

    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category created successfully'
        ], 201);
    }

    public function categoryUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::find($request->id);
        $category->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category updated successfully'
        ], 200);
    }

    public function deleteCategory($id)
    {
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ], 200);
    }

    // ==================== PRODUCT API ====================

    public function productList()
    {
        $products = Product::with(['category', 'sizes', 'discounts'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Products retrieved successfully'
        ], 200);
    }

    public function productDetail($id)
    {
        $product = Product::with(['category', 'sizes', 'discounts'])->find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product retrieved successfully'
        ], 200);
    }

    public function createProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'price_khr' => 'nullable|numeric|min:0',
            'price_usd' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product created successfully'
        ], 201);
    }

    public function updateProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:products,id',
            'name' => 'sometimes|string|max:255',
            'qty' => 'sometimes|integer|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'price_khr' => 'nullable|numeric|min:0',
            'price_usd' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::find($request->id);
        $product->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product updated successfully'
        ], 200);
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], 200);
    }

    // ==================== PRODUCT SIZE API ====================

    public function productSizeList($productId)
    {
        $sizes = ProductSize::where('product_id', $productId)->get();
        
        return response()->json([
            'success' => true,
            'data' => $sizes,
            'message' => 'Product sizes retrieved successfully'
        ], 200);
    }

    public function createProductSize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'size' => 'required|string|max:10',
            'price' => 'nullable|numeric|min:0',
            'price_khr' => 'nullable|numeric|min:0',
            'price_usd' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $size = ProductSize::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $size,
            'message' => 'Product size created successfully'
        ], 201);
    }

    public function updateProductSize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:product_sizes,id',
            'size' => 'sometimes|string|max:10',
            'price' => 'nullable|numeric|min:0',
            'price_khr' => 'nullable|numeric|min:0',
            'price_usd' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $size = ProductSize::find($request->id);
        $size->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $size,
            'message' => 'Product size updated successfully'
        ], 200);
    }

    public function deleteProductSize($id)
    {
        $size = ProductSize::find($id);
        
        if (!$size) {
            return response()->json([
                'success' => false,
                'message' => 'Product size not found'
            ], 404);
        }

        $size->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product size deleted successfully'
        ], 200);
    }

    // ==================== DISCOUNT API ====================

    public function discountList()
    {
        $discounts = Discount::with('product')->get();
        
        return response()->json([
            'success' => true,
            'data' => $discounts,
            'message' => 'Discounts retrieved successfully'
        ], 200);
    }

    public function createDiscount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $discount = Discount::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $discount,
            'message' => 'Discount created successfully'
        ], 201);
    }

    public function updateDiscount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:discounts,id',
            'product_id' => 'sometimes|exists:products,id',
            'discount_percentage' => 'sometimes|numeric|min:0|max:100',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $discount = Discount::find($request->id);
        $discount->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $discount,
            'message' => 'Discount updated successfully'
        ], 200);
    }

    public function deleteDiscount($id)
    {
        $discount = Discount::find($id);
        
        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Discount not found'
            ], 404);
        }

        $discount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Discount deleted successfully'
        ], 200);
    }

    // ==================== ORDER API ====================

    public function orderList()
    {
        $orders = Order::with(['product', 'user'])->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Orders retrieved successfully'
        ], 200);
    }

    public function orderDetail($id)
    {
        $order = Order::with(['product', 'user'])->find($id);
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order retrieved successfully'
        ], 200);
    }

    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string|max:10',
            'totalprice' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,card,online',
            'order_type' => 'required|string|in:takeaway,delivery,dine-in',
            'notes' => 'nullable|string',
            'delivery_location_id' => 'nullable|exists:delivery_fees,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $orderData = $request->all();
        $orderData['order_code'] = 'ORD-' . time() . rand(100, 999);
        $orderData['status'] = 'pending';

        $order = Order::create($orderData);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order created successfully'
        ], 201);
    }

    public function updateOrderStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:orders,id',
            'status' => 'required|string|in:pending,processing,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::find($request->id);
        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order status updated successfully'
        ], 200);
    }

    public function deleteOrder($id)
    {
        $order = Order::find($id);
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ], 200);
    }

    // ==================== TAX API ====================

    public function taxList()
    {
        $taxes = TaxSetting::all();
        
        return response()->json([
            'success' => true,
            'data' => $taxes,
            'message' => 'Tax settings retrieved successfully'
        ], 200);
    }

    public function createTax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $tax = TaxSetting::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $tax,
            'message' => 'Tax setting created successfully'
        ], 201);
    }

    public function updateTax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:tax_settings,id',
            'name' => 'sometimes|string|max:255',
            'percentage' => 'sometimes|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $tax = TaxSetting::find($request->id);
        $tax->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $tax,
            'message' => 'Tax setting updated successfully'
        ], 200);
    }

    public function deleteTax($id)
    {
        $tax = TaxSetting::find($id);
        
        if (!$tax) {
            return response()->json([
                'success' => false,
                'message' => 'Tax setting not found'
            ], 404);
        }

        $tax->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tax setting deleted successfully'
        ], 200);
    }

    // ==================== FEEDBACK API ====================

    public function createFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $feedback = Review::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $feedback,
            'message' => 'Feedback created successfully'
        ], 201);
    }

    public function feedbackList($productId = null)
    {
        $query = Review::with('product', 'user');
        
        if ($productId) {
            $query->where('product_id', $productId);
        }
        
        $feedbacks = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $feedbacks,
            'message' => 'Feedbacks retrieved successfully'
        ], 200);
    }
}
