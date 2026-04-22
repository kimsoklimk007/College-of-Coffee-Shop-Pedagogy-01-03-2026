<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with('user')
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->category, function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->when($request->start_date, function ($query) use ($request) {
                $query->where('transaction_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->where('transaction_date', '<=', $request->end_date);
            })
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $totalIncome = Transaction::where('type', 'income')
            ->when($request->start_date, function ($query) use ($request) {
                $query->where('transaction_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->where('transaction_date', '<=', $request->end_date);
            })
            ->sum('amount');

        $totalExpense = Transaction::where('type', 'expense')
            ->when($request->start_date, function ($query) use ($request) {
                $query->where('transaction_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->where('transaction_date', '<=', $request->end_date);
            })
            ->sum('amount');

        return view('admin.transaction.index', compact('transactions', 'totalIncome', 'totalExpense'));
    }

    public function create()
    {
        return view('admin.transaction.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,KHR',
            'exchange_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
        ]);

        $exchangeRate = $validated['currency'] === 'KHR' ? ($validated['exchange_rate'] ?? 4100) : 1;
        $amountKhr = $validated['currency'] === 'KHR' ? $validated['amount'] : ($validated['amount'] * $exchangeRate);
        $amountUsd = $validated['currency'] === 'USD' ? $validated['amount'] : ($validated['amount'] / $exchangeRate);

        Transaction::create([
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $amountUsd,
            'currency' => 'USD',
            'exchange_rate' => $exchangeRate,
            'amount_khr' => $amountKhr,
            'description' => $validated['description'],
            'transaction_date' => $validated['transaction_date'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('transaction.index')->with('success', 'Transaction added successfully!');
    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('admin.transaction.edit', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,KHR',
            'exchange_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
        ]);

        $transaction = Transaction::findOrFail($id);

        $exchangeRate = $validated['currency'] === 'KHR' ? ($validated['exchange_rate'] ?? 4100) : 1;
        $amountKhr = $validated['currency'] === 'KHR' ? $validated['amount'] : ($validated['amount'] * $exchangeRate);
        $amountUsd = $validated['currency'] === 'USD' ? $validated['amount'] : ($validated['amount'] / $exchangeRate);

        $transaction->update([
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $amountUsd,
            'currency' => 'USD',
            'exchange_rate' => $exchangeRate,
            'amount_khr' => $amountKhr,
            'description' => $validated['description'],
            'transaction_date' => $validated['transaction_date'],
        ]);

        return redirect()->route('transaction.index')->with('success', 'Transaction updated successfully!');
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
        return redirect()->route('transaction.index')->with('success', 'Transaction deleted successfully!');
    }

    public function summary(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        $incomeByCategory = Transaction::where('type', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('category')
            ->select('category')
            ->selectRaw('SUM(amount) as total')
            ->get();

        $expenseByCategory = Transaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('category')
            ->select('category')
            ->selectRaw('SUM(amount) as total')
            ->get();

        $totalIncome = $incomeByCategory->sum('total');
        $totalExpense = $expenseByCategory->sum('total');
        $profit = $totalIncome - $totalExpense;

        return view('admin.transaction.summary', compact(
            'incomeByCategory', 'expenseByCategory', 'totalIncome', 'totalExpense', 'profit', 'startDate', 'endDate'
        ));
    }
}
