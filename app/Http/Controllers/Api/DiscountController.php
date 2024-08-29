<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\DiscountRequest;
use App\Models\Discount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    //
    public function index()
    {
        $discounts = Discount::all();
        return response()->json($discounts);
    }

    public function store(DiscountRequest $request)
    {
        $validatedData = $request->validated();
        $discount = Discount::create($validatedData);
        return response()->json([
            'message' => 'Discount created successfully',
            'discount' => $discount
            ]);
        }

    public function update(DiscountRequest $request, $id)
    {
        $validatedData = $request->validated();
        $discount = Discount::findOrFail($id);
        $discount->update($validatedData);
        return response()->json(['message' => 'Discount updated successfully', 'discount' => $discount]);
    }

    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();
        return response()->json(['message' => 'Discount deleted successfully']);
    }

}
