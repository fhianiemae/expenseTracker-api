<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->transactions()->with('category');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('from')) {
            $query->whereDate('occurred_at', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->whereDate('occurred_at', '<=', $request->to);
        }

        return $query->orderBy('occurred_at', 'desc')->paginate(10);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|numeric|min:0',
            'occurred_at' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // category ownership check
        if ($data['category_id']) {
            $category = $request->user()->categories()->find($data['category_id']);
            if (! $category) {
                abort(403, 'Category not owned by user');
            }
        }

        $transaction = $request->user()->transactions()->create($data);

        return response()->json($transaction, 201);
    }

    public function show(Request $request, Transaction $transaction)
    {
        $this->authorizeTransaction($request, $transaction);
        return $transaction->load('category');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorizeTransaction($request, $transaction);

        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'type'        => 'in:income,expense',
            'amount'      => 'numeric|min:0',
            'occurred_at' => 'date',
            'description' => 'nullable|string',
        ]);

        if (isset($data['category_id'])) {
            $category = $request->user()->categories()->find($data['category_id']);
            if (! $category) {
                abort(403, 'Category not owned by user');
            }
        }

        $transaction->update($data);

        return response()->json($transaction);
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        $this->authorizeTransaction($request, $transaction);
        $transaction->delete();
        return response()->json(['message' => 'Deleted']);
    }

    protected function authorizeTransaction(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }
    }
}
