<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Store::all(), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crud-store');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $store = Store::create($validated);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        //
    }


    public function getStorePrices(Request $request)
    {
        $request->validate([
            'product_code' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius / 1000;
        $productCode = $request->product_code;

        $stores = Store::selectRaw("
            stores.id, stores.name, stores.address, transactions.price, transactions.updated_at,
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
            * cos(radians(longitude) - radians(?)) 
            + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
            ->join('transactions', 'stores.id', '=', 'transactions.store_id')
            ->where('transactions.product_code', $productCode)
            ->having('distance', '<=', $radius)
            ->orderBy('transactions.updated_at', 'desc')
            ->get();

        return response()->json($stores);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $store->update($validated);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $store->delete();
        return redirect()->back();
    }
}
