<?php
namespace App\Models;

use Core\Model;

class Order extends Model {
    protected ?int $id = null;
    protected int $student_id;
    protected float $total_value;
    protected string $status;
    
    protected ?Delivery $delivery = null;
    protected array $items = [];

    public function __construct(int $student_id, float $total_value) {
        $this->student_id = $student_id;
        $this->total_value = $total_value;
        $this->status = 'Awaiting Payment';
    }

    public function addItem(OrderItem $item) {
        $this->items[] = $item;
    }

    public function setDelivery(Delivery $delivery) {
        $this->delivery = $delivery;
    }

    public function setId(int $id) { $this->id = $id; }
}