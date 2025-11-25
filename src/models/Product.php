<?php
namespace App\Models;

use Core\Model;

class Product extends Model {
    protected ?int $id = null;
    protected string $name;
    protected float $price;
    protected int $stock_quantity;

    public function __construct(string $name, float $price, int $stock) {
        $this->name = $name;
        $this->price = $price;
        $this->stock_quantity = $stock;
    }

    public function setId(int $id) { $this->id = $id; }
}