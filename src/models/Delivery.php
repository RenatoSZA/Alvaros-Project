<?php
namespace App\Models;

use Core\Model;

class Delivery extends Model {
    protected ?int $id = null;
    protected string $street;
    protected string $zip_code;
    protected string $shipping_type;
    protected ?string $tracking_code = null;

    public function __construct(string $street, string $zip_code, string $shipping_type) {
        $this->street = $street;
        $this->zip_code = $zip_code;
        $this->shipping_type = $shipping_type;
    }
}