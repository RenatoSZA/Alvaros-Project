<?php
namespace App\Services;

use Core\SessionManager;

class CartService {
    
    public function add(int $productId, int $qty, float $price, string $name) {
        $cart = SessionManager::get('cart') ?? [];

        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] += $qty;
        } else {
            $cart[$productId] = [
                'id' => $productId,
                'qty' => $qty,
                'price' => $price,
                'name' => $name
            ];
        }
        
        SessionManager::set('cart', $cart);
    }

    public function getItems(): array {
        return SessionManager::get('cart') ?? [];
    }

    public function getTotal(): float {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }

    public function clear() {
        SessionManager::remove('cart');
    }
}