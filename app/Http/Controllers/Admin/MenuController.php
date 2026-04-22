<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductSize;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with(['products.sizes', 'products.discounts'])->get();

        return view('admin.menu.index', compact('categories'));
    }

    public function generateTelegramMenu()
    {
        $categories = Category::with(['products.sizes', 'products.discounts'])->get();

        $menuText = "=== COFFEE SHOP MENU ===\n\n";

        foreach ($categories as $category) {
            $menuText .= "=== " . $category->name . " ===\n";

            foreach ($category->products as $product) {
                $menuText .= "\n" . $product->name . "\n";

                foreach ($product->sizes as $size) {
                    $price = $size->price_usd;
                    $sizeName = $size->size ?? 'Regular';

                    $discount = $product->discounts->firstWhere('product_size_id', $size->id);
                    if ($discount) {
                        $discountedPrice = $price * (1 - $discount->discount_percent / 100);
                        $menuText .= "  - $sizeName: $" . number_format($price, 2) . " -> $" . number_format($discountedPrice, 2) . " (" . $discount->discount_percent . "% OFF)\n";
                    } else {
                        $menuText .= "  - $sizeName: $" . number_format($price, 2) . "\n";
                    }
                }

                if ($product->sizes->isEmpty()) {
                    $price = $product->price_usd;
                    $discount = $product->discounts->first();
                    if ($discount) {
                        $discountedPrice = $price * (1 - $discount->discount_percent / 100);
                        $menuText .= "  Price: $" . number_format($price, 2) . " -> $" . number_format($discountedPrice, 2) . "\n";
                    } else {
                        $menuText .= "  Price: $" . number_format($price, 2) . "\n";
                    }
                }
            }
            $menuText .= "\n";
        }

        $menuText .= "\n=== TO ORDER ===\nSend your order in this format:\nProduct Name - Size - Quantity\n\nExample:\nLatte - Large - 2";

        return response()->json([
            'success' => true,
            'menu' => $menuText,
            'html' => view('admin.menu.partials.menu_items', compact('categories'))->render()
        ]);
    }

    public function exportTelegram()
    {
        $categories = Category::with(['products.sizes', 'products.discounts'])->get();

        $menuText = "=== COFFEE SHOP MENU ===\n\n";

        foreach ($categories as $category) {
            $menuText .= "=== " . $category->name . " ===\n";

            foreach ($category->products as $product) {
                $menuText .= "\n" . $product->name . "\n";

                foreach ($product->sizes as $size) {
                    $price = $size->price_usd;
                    $sizeName = $size->size ?? 'Regular';
                    $menuText .= "  - $sizeName: $" . number_format($price, 2) . "\n";
                }

                if ($product->sizes->isEmpty()) {
                    $menuText .= "  Price: $" . number_format($product->price_usd, 2) . "\n";
                }
            }
            $menuText .= "\n";
        }

        $menuText .= "\n=== TO ORDER ===\nSend your order in this format:\nProduct Name - Size - Quantity";

        return response()->json([
            'success' => true,
            'menu_text' => $menuText
        ]);
    }

    public function sendToTelegram(Request $request)
    {
        $request->validate([
            'bot_token' => 'required',
            'chat_id' => 'required',
            'menu_text' => 'required'
        ]);

        $botToken = $request->bot_token;
        $chatId = $request->chat_id;
        $menuText = $request->menu_text;

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $response = \Http::post($url, [
            'chat_id' => $chatId,
            'text' => $menuText,
            'parse_mode' => 'Markdown'
        ]);

        if ($response->successful()) {
            return back()->with('success', 'Menu sent to Telegram successfully!');
        }

        return back()->with('error', 'Failed to send to Telegram: ' . $response->body());
    }
}
