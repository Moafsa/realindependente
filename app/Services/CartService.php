<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get cart items.
     *
     * @return array
     */
    public function getCart(): array
    {
        return Session::get('cart', []);
    }

    /**
     * Add product to cart.
     *
     * @param Product $product
     * @param int $quantity
     * @return bool
     */
    public function add(Product $product, int $quantity = 1): bool
    {
        // Check if product is available
        if (!$product->is_available) {
            return false;
        }

        // Check stock
        if ($product->stock_quantity !== null && $product->stock_quantity < $quantity) {
            return false;
        }

        $cart = $this->getCart();
        
        if (isset($cart[$product->id])) {
            $newQuantity = $cart[$product->id]['quantity'] + $quantity;
            
            // Check stock again for updated quantity
            if ($product->stock_quantity !== null && $product->stock_quantity < $newQuantity) {
                return false;
            }
            
            $cart[$product->id]['quantity'] = $newQuantity;
        } else {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
            ];
        }

        Session::put('cart', $cart);
        
        return true;
    }

    /**
     * Remove product from cart.
     *
     * @param Product $product
     * @return bool
     */
    public function remove(Product $product): bool
    {
        $cart = $this->getCart();
        
        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            Session::put('cart', $cart);
            return true;
        }
        
        return false;
    }

    /**
     * Update product quantity in cart.
     *
     * @param Product $product
     * @param int $quantity
     * @return bool
     */
    public function update(Product $product, int $quantity): bool
    {
        // Check stock
        if ($product->stock_quantity !== null && $product->stock_quantity < $quantity) {
            return false;
        }

        $cart = $this->getCart();
        
        if (isset($cart[$product->id])) {
            if ($quantity <= 0) {
                return $this->remove($product);
            }
            
            $cart[$product->id]['quantity'] = $quantity;
            Session::put('cart', $cart);
            return true;
        }
        
        return false;
    }

    /**
     * Clear cart.
     *
     * @return void
     */
    public function clear(): void
    {
        Session::forget('cart');
    }

    /**
     * Get cart total.
     *
     * @return float
     */
    public function getTotal(): float
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $total += $product->price * $item['quantity'];
            }
        }

        return $total;
    }

    /**
     * Get cart items count.
     *
     * @return int
     */
    public function getItemsCount(): int
    {
        $cart = $this->getCart();
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    /**
     * Get cart items with product data.
     *
     * @return array
     */
    public function getItemsWithProducts(): array
    {
        $cart = $this->getCart();
        $items = [];

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity'],
                ];
            }
        }

        return $items;
    }

    /**
     * Check if cart is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->getCart());
    }

    /**
     * Validate cart items (check availability and stock).
     *
     * @return array Array with 'valid' boolean and 'errors' array
     */
    public function validate(): array
    {
        $cart = $this->getCart();
        $errors = [];

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            
            if (!$product) {
                $errors[] = "Produto ID {$item['product_id']} não encontrado.";
                continue;
            }

            if (!$product->is_available) {
                $errors[] = "Produto '{$product->name}' não está disponível.";
                continue;
            }

            if ($product->stock_quantity !== null && $product->stock_quantity < $item['quantity']) {
                $errors[] = "Produto '{$product->name}' não possui estoque suficiente.";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}

