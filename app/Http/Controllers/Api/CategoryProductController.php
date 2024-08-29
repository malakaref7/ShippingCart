<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class CategoryProductController extends Controller
{
    // this fn will return all categories
    public function index(Category $category)
    {
        return $category->product();
    }
    // this fn shows each category with it's own products
    public function showAll()
    {
        return Category::with("product")->get();
    }
    public function update(Category $category, Request $request)
    {
        $category->update($request->all());
        return $category;
    }

    public function destroy(Category $category, Product $product)
    {
        return $product->delete();
    }
}
