<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingRequest;
use App\Models\ShippingRate;
use Illuminate\Http\Request;


class ShippingRateController extends Controller
{
    public function index()
    {
        $shippingRates = ShippingRate::all();
        return $shippingRates;
    }
    
    public function store(ShippingRequest $request)
    {
        $validatedData = $request->validated();
        $shippingRate = new ShippingRate($validatedData);
        $shippingRate->save();
        return response()->json(['message' => 'Shipping rate created successfully', 'data' => $shippingRate]);
    }
    
    public function update(ShippingRequest $request, $id)
    {
        $shippingRate = ShippingRate::findOrFail($id);
        $shippingRate->update($request->validated());
        return response()->json(['message' => 'Shipping rate updated successfully', 'data' => $shippingRate]);
    }
}
