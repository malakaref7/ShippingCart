<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // create category
        public function store(Request $request)
        {
            return Category::create($request->all());
        }
        // will return all categories 
        public function index()
        {
            return Category::all();
        }
        // will return category by category id
        public function show(Category $category)
        {
            return $category;
        }
        public function update(Category $category, Request $request)
        {
            $category->update($request->all());
            return $category;
        }
        public function destroy(Category $category)
        {
            $category->delete();
            return [
                'message' => 'category was deleted successfully!'
            ];
        }
}
