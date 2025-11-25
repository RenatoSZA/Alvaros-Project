<?php
namespace App\Mappers;

use Core\Database;
use App\Models\Order;
use PDO;
use Exception;
use Throwable;

class OrderMapper {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function save(Order $order): bool {
        try {
            $this->pdo->beginTransaction();

            $sqlOrder = "INSERT INTO orders (student_id, total_value, status, created_at) 
                         VALUES (:sid, :total, :status, NOW())";
            $stmt = $this->pdo->prepare($sqlOrder);
            $stmt->execute([
                ':sid'    => $order->student_id, 
                ':total'  => $order->total_value,
                ':status' => $order->status
            ]);
            
            $orderId = (int)$this->pdo->lastInsertId();
            $order->setId($orderId);

            $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, unit_price) 
                        VALUES (:oid, :pid, :qtd, :price)";
            $stmtItem = $this->pdo->prepare($sqlItem);

            foreach ($order->items as $item) {
                $stmtItem->execute([
                    ':oid'   => $orderId,
                    ':pid'   => $item->product_id, 
                    ':qtd'   => $item->quantity,
                    ':price' => $item->unit_price
                ]);
            }

            // 3. Salva Entrega
            if ($order->delivery) {
                $sqlDel = "INSERT INTO deliveries (order_id, street, zip_code, shipping_type) 
                           VALUES (:oid, :street, :zip, :type)";
                $stmtDel = $this->pdo->prepare($sqlDel);
                $stmtDel->execute([
                    ':oid'    => $orderId,
                    ':street' => $order->delivery->street,
                    ':zip'    => $order->delivery->zip_code,
                    ':type'   => $order->delivery->shipping_type
                ]);
            }

            $this->pdo->commit();
            return true;

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log("Erro ao salvar pedido: " . $e->getMessage());
            throw new Exception("Falha ao processar pedido.");
        }
    }
}