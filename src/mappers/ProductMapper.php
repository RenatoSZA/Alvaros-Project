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
            $prod = $this->mapRowToProduct($row);
            $results[] = $prod;
        }
        return $results;
    }

    public function find(int $id): ?Product {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        return $this->mapRowToProduct($row);
    }

    public function decreaseStock(int $id, int $quantity) {
        $sql = "UPDATE products SET stock_quantity = stock_quantity - :qtd WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':qtd' => $quantity, ':id' => $id]);
    }

    private function mapRowToProduct(array $row): Product {
        $prod = new Product($row['name'], (float)$row['price'], (int)$row['stock_quantity']);
        $prod->setId((int)$row['id']);
        
        if (isset($row['description'])) {
            $prod->setDescription($row['description']);
        }
        
        if (isset($row['image_url'])) {
            $prod->setImageUrl($row['image_url']);
        }
        
        return $prod;
    }
}