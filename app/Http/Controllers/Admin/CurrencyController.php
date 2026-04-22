<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
    /**
     * Get latest exchange rate for USD to KHR
     * Uses Cambodia market rates or falls back to reliable alternatives
     */
    public function getExchangeRate(): JsonResponse
    {
        try {
            // Try to get from cache first (30 minutes)
            $cachedRate = Cache::get('usd_khr_exchange_rate');
            $cachedSource = Cache::get('usd_khr_rate_source');
            
            if ($cachedRate) {
                return response()->json([
                    'success' => true,
                    'rate' => $cachedRate,
                    'source' => $cachedSource ?? 'cache',
                    'cached' => true,
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            // Try to fetch from multiple sources
            $rate = $this->fetchFromCambodiaSources();
            
            if (!$rate) {
                // Fallback to default rate
                $rate = 4100;
                $source = 'default';
            } else {
                $source = 'nbc';
            }

            // Cache for 30 minutes
            Cache::put('usd_khr_exchange_rate', $rate, now()->addMinutes(30));
            Cache::put('usd_khr_rate_source', $source, now()->addMinutes(30));

            return response()->json([
                'success' => true,
                'rate' => $rate,
                'source' => $source,
                'cached' => false,
                'timestamp' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch exchange rate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return default rate on error
            return response()->json([
                'success' => true,
                'rate' => 4100,
                'source' => 'default_fallback',
                'cached' => false,
                'error' => 'Using default rate due to fetch error',
                'timestamp' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Fetch exchange rate from Cambodia sources
     */
    private function fetchFromCambodiaSources(): ?float
    {
        // National Bank of Cambodia unofficial rate (common market rate)
        // Most money changers in Cambodia use around 4100-4150 KHR per USD
        
        try {
            // Try to get from a reliable API (Open Exchange Rates or similar)
            // For Cambodia specifically, we can use alternative reliable sources
            
            // Option 1: Try frankfurter.app (free, reliable, no API key needed)
            $response = Http::timeout(10)->get('https://api.frankfurter.app/latest', [
                'from' => 'USD',
                'to' => 'KHR'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rates']['KHR'])) {
                    $rate = $data['rates']['KHR'];
                    Log::info('Exchange rate fetched from Frankfurter API', ['rate' => $rate]);
                    return round($rate, 2);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Frankfurter API failed', ['error' => $e->getMessage()]);
        }

        try {
            // Option 2: Try exchangerate-api.com (free tier available)
            $response = Http::timeout(10)->get('https://api.exchangerate-api.com/v4/latest/USD');

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rates']['KHR'])) {
                    $rate = $data['rates']['KHR'];
                    Log::info('Exchange rate fetched from ExchangeRate API', ['rate' => $rate]);
                    return round($rate, 2);
                }
            }
        } catch (\Exception $e) {
            Log::warning('ExchangeRate API failed', ['error' => $e->getMessage()]);
        }

        // Option 3: Use common Cambodia market rate
        // Most money changers in Phnom Penh use approximately 4100-4150
        $marketRate = 4100;
        Log::info('Using Cambodia market default rate', ['rate' => $marketRate]);
        
        return $marketRate;
    }

    /**
     * Convert currency between USD and KHR
     */
    public function convertCurrency(): JsonResponse
    {
        try {
            $amount = request('amount', 0);
            $from = strtoupper(request('from', 'USD'));
            $to = strtoupper(request('to', 'KHR'));

            if ($amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid amount provided'
                ], 400);
            }

            // Get current rate
            $rateResponse = $this->getExchangeRate();
            $rateData = $rateResponse->getData();
            $rate = $rateData->rate ?? 4100;

            // Perform conversion
            if ($from === 'USD' && $to === 'KHR') {
                $result = $amount * $rate;
            } elseif ($from === 'KHR' && $to === 'USD') {
                $result = $amount / $rate;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unsupported currency conversion'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'amount' => $amount,
                'from' => $from,
                'to' => $to,
                'result' => round($result, 2),
                'rate' => $rate,
                'timestamp' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            Log::error('Currency conversion failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Conversion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear cached exchange rate
     */
    public function clearCache(): JsonResponse
    {
        Cache::forget('usd_khr_exchange_rate');
        Cache::forget('usd_khr_rate_source');

        return response()->json([
            'success' => true,
            'message' => 'Exchange rate cache cleared'
        ]);
    }
}
