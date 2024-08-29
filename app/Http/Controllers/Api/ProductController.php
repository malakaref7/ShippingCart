<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\ProductRequest;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //

    public function index()
    {
        return Product::with('category')->get();
    }
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();

        $product = Product::create($validated);

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }
    public function show(Product $product)
    {
        return $product;
    }
    public function update(Product $product, Request $request)
    {
        $product->update($request->all());
        return $product;
    }
    public function destroy(Product $product)
    {
        $product->delete();
        return [
            'message' => 'product was deleted successfully!'
        ];
    }

}
