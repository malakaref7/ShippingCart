<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Http\Requests\CartRequest;
use App\Models\ShippingRate;

class CartController extends Controller
{
    public function store(CartRequest $request)
    {
        $products = $request->input('products'); 
        $cart = Cart::create(); 

        foreach ($products as $productData) {
            $product = Product::find($productData['product_id']);
            if ($product) {
                // attach product to cart
                $cart->products()->attach($product->id, ['quantity' => $productData['quantity']]);
            } else {
                return response()->json(['error' => 'Product not found', 'product_id' => $productData['product_id']], 404);
            }
        } 

        // to teturn the cart details once we create it
        $subtotal = $this->calculateSubtotal($cart->products);
        $totalShipping = $this->calculateShipping($cart->products);
        $discountResult = $this->calculateDiscounts($cart->products, $totalShipping);
        $totalDiscount = $discountResult['totalDiscount'];
        $discountDetails = $discountResult['discountDetails'];
        $vat = $this->calculateVAT($subtotal);
        $total = $subtotal + $totalShipping - $totalDiscount + $vat;
    
        // Return the detailed cart information
        return response()->json([
            'subtotal' => $subtotal,
            'shipping' => $totalShipping,
            'vat' => $vat,
            'discounts' => $discountDetails, // Return detailed discount breakdown
            'total' => $total,
        ]);
    }

    public function index()
    {
        $cartItems = Cart::with('products')->get();
        return response()->json($cartItems);
    }

    public function show(Cart $cart)
    {
        // Retrieve the cart with its associated products
        $cart = Cart::with('products')->find($cart);
        return $cart;
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::find($id);
        $cart->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json(['message' => 'Cart updated', 'cart' => $cart]);
    }

    public function remove($id)
    {
        Cart::destroy($id);
        return response()->json(['message' => 'Product removed from cart']);
    }

    public function getCartDetails($cartId)
    {
        // Retrieve the cart with its products using the cart ID
        $cart = Cart::with('products')->find($cartId);
    
        // Check if the cart exists and has products
        if (!$cart || $cart->products->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty',
                'subtotal' => 0,
                'shipping' => 0,
                'vat' => 0,
                'discounts' => [],
                'total' => 0,
            ]);
        }
    
        $subtotal = $this->calculateSubtotal($cart->products);
        $totalShipping = $this->calculateShipping($cart->products);
        $discountResult = $this->calculateDiscounts($cart->products, $totalShipping);
        $totalDiscount = $discountResult['totalDiscount'];
        $discountDetails = $discountResult['discountDetails'];
        $vat = $this->calculateVAT($subtotal);
        $total = $subtotal + $totalShipping - $totalDiscount + $vat;
    
        // Return the detailed cart information
        return response()->json([
            'subtotal' => $subtotal,
            'shipping' => $totalShipping,
            'vat' => $vat,
            'discounts' => $discountDetails, // Return detailed discount breakdown
            'total' => $total,
        ]);
    }
    
    public function calculateSubtotal($products)
    {
        return $products->sum(function ($product) {
            return $product->price * $product->pivot->quantity;
            
        });
    }
    
    public function calculateShipping($cartItems)
    {
        $totalShipping = 0;
        foreach ($cartItems as $item) {
            $shippingRate = $this->getShippingRate($item['shipped_from']);
            $totalShipping += $shippingRate * ($item['weight'] * 10); 
        }
        return $totalShipping;
    }
    
    private function calculateVAT($subtotal)
    {
        return $subtotal * 0.14;
    }

    public function getShippingRate($shipped_from)
    {
        $shippingRate = ShippingRate::where('country_code', $shipped_from)->first();
        
        return $shippingRate ? $shippingRate->rate : 0;
    }

    private function calculateDiscounts($cartItems, $totalShipping)
    {
        $totalDiscount = 0;
        $discountDetails = [];
        $discounts = Discount::all();
    
        foreach ($discounts as $discount) {
            $discountValue = $this->applyDiscount($cartItems, $discount, $totalShipping);
    
            if ($discountValue > 0) {
                $totalDiscount += $discountValue;
                $discountDetails[$discount->name] = -$discountValue;
            }
        }
    
        return [
            'totalDiscount' => $totalDiscount,
            'discountDetails' => $discountDetails
        ];
    }
    
    private function applyDiscount($cartItems, $discount, $totalShipping)
    {
        $value = 0;
        // if ($discount->quantity_required) {
            
           $conditionItems = $cartItems->filter(function($item) use ($discount) {
                return strtolower($item->category->name) == strtolower($discount->condition);
            })->values();

            $itemsCount = $conditionItems->sum('pivot.quantity')* $conditionItems->count();

            
            if ($itemsCount < $discount->quantity_required && strtolower($discount->applyTo) != strtolower('Shipping')) {
                return 0;
            }
        // }
    
        // discount to the specified product
        $itemDiscount = $cartItems->filter(function($item) use ($discount) {
            return strtolower($item->type) == strtolower($discount->applyTo);
        })->values()->first();
        if ($itemDiscount) {
            if ($discount->type == 'fixed') {
                $value += $discount->value;
            } else {
                $value += ($discount->value / 100) * $itemDiscount->price * $itemDiscount->pivot->quantity;
            }
        }

        // discount on shipping 
        if (strtolower($discount->applyTo) == strtolower('Shipping') && $conditionItems->count()>2) {
            if ($discount->type == 'fixed') {
                $value += $discount->value;
            } else {
                $value += ($discount->value / 100) * $totalShipping;
            }
        }
    
        return $value;
    }
}
