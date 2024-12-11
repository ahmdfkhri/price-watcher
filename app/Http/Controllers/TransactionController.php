<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:255',
            'store_id' => 'exists:stores,id|string|max:255',
            'product_name' => 'required_if:product_code,!exists|string|max:255',
            'price' => 'required|numeric',
        ]);

        $product = Product::firstOrCreate(
            ['code' => $validated['product_code']], // Search for this product by code
            ['name' => $validated['product_name']]  // If not found, create with this name
        );

        $transaction = Transaction::create([
            'product_code' => $product->code,
            'store_id' => $validated['store_id'],
            'price' => $validated['price'],
        ]);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
