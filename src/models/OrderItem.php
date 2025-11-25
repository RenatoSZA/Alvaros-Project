<?php
namespace App\Models;

use Core\Model;

class OrderItem extends Model {
    protected ?int $id = null;
    protected int $product_id;
    protected int $quantity;
    protected float $unit_price;

    public function __construct(int $product_id, int $quantity, float $unit_price) {
        $this->product_id = $product_id;
        $this->quantity = $quantity;
        $this->unit_price = $unit_price;
    }
}