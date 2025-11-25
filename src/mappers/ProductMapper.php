<?php
namespace App\Mappers;

use Core\Database;
use App\Models\Product;
use PDO;

class ProductMapper {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM products");
        $results = [];
        while ($row = $stmt->fetch()) {
            $prod = new Product($row['name'], $row['price'], $row['stock_quantity']);
            $prod->setId($row['id']);
            $results[] = $prod;
        }
        return $results;
    }

    public function find(int $id): ?Product {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        $prod = new Product($row['name'], $row['price'], $row['stock_quantity']);
        $prod->setId($row['id']);
        return $prod;
    }

    public function decreaseStock(int $id, int $quantity) {
        $sql = "UPDATE products SET stock_quantity = stock_quantity - :qtd WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':qtd' => $quantity, ':id' => $id]);
    }
}