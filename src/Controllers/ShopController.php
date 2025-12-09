<?php
namespace App\Controllers;

use App\Mappers\ProductMapper;
use App\Services\CartService;
use Core\SessionManager;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mappers\OrderMapper;

class ShopController {

    // Renderiza a View Principal
    public function index() {
        if (!SessionManager::isLogged()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        $userName = SessionManager::get('user_name');
        require __DIR__ . '/../../views/shop.php';
    }

    // API: Retorna lista de produtos em JSON
    public function apiList() {
        $mapper = new ProductMapper();
        $products = $mapper->findAll();
        
        // Ajuste para expor propriedades protegidas como array público
        $data = array_map(function($p) {
            return [
                'id' => $p->id, // Usando __get magico
                'name' => $p->name,
                'price' => $p->price,
                'description' => $p->description ?? '',
                'image_url' => $p->image_url ?? ''
            ];
        }, $products);

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // API: Retorna carrinho atual
    public function apiGetCart() {
        $cartService = new CartService();
        header('Content-Type: application/json');
        echo json_encode($cartService->getItems());
        exit;
    }

    // API: Adicionar item
    public function apiAdd() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            exit;
        }

        $mapper = new ProductMapper();
        $product = $mapper->find((int)$id);

        if ($product) {
            $cartService = new CartService();
            // Garante que o ID seja inteiro para consistência na sessão
            $cartService->add((int)$product->id, 1, (float)$product->price, $product->name);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
        }
        exit;
    }

    // API: Remover item (diminuir qtd)
    public function apiRemove() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        $cartService = new CartService();
        $cartService->removeOne((int)$id);
        
        echo json_encode(['success' => true]);
        exit;
    }

    // API: Checkout (Simples)
    public function apiCheckout() {
        if (!SessionManager::isLogged()) {
            echo json_encode(['success' => false, 'message' => 'Login required']);
            exit;
        }

        $cartService = new CartService();
        $items = $cartService->getItems();
        
        if (empty($items)) {
            echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
            exit;
        }

        try {
            $total = $cartService->getTotal();
            $studentId = SessionManager::get('user_id');

            $order = new Order($studentId, $total);
            
            foreach ($items as $item) {
                // Assuming OrderItem constructor: product_id, quantity, unit_price
                $orderItem = new OrderItem($item['id'], $item['qty'], $item['price']);
                $order->addItem($orderItem);
            }

            $orderMapper = new OrderMapper();
            $orderMapper->save($order);
            
            $cartService->clear();

            echo json_encode(['success' => true, 'orderId' => $order->id]);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}