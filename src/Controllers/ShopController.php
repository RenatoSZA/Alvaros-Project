<?php
namespace App\Controllers;

use App\Mappers\ProductMapper;
use App\Services\CartService;
use Core\SessionManager;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mappers\OrderMapper;

class ShopController {

    public function index() {
        if (!SessionManager::isLogged()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        $userName = SessionManager::get('user_name');
        require __DIR__ . '/../../views/shop.php';
    }

    public function apiList() {
        $mapper = new ProductMapper();
        $products = $mapper->findAll();
        
        $data = array_map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'description' => $p->description,
                'image_url' => $p->image_url
            ];
        }, $products);

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function apiGetCart() {
        $service = new CartService();
        header('Content-Type: application/json');
        echo json_encode($service->getItems());
        exit;
    }

    public function apiAdd() {
        $id = $_GET['id'] ?? null;
        $mapper = new ProductMapper();
        $product = $mapper->find((int)$id);

        if ($product) {
            $service = new CartService();
            $service->add((int)$product->id, 1, (float)$product->price, $product->name);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    public function apiRemove() {
        $id = $_GET['id'] ?? null;
        $service = new CartService();
        $service->removeOne((int)$id);
        echo json_encode(['success' => true]);
        exit;
    }

    public function apiCheckout() {
        if (!SessionManager::isLogged()) exit;

        $service = new CartService();
        $items = $service->getItems();
        
        if (empty($items)) {
            echo json_encode(['success' => false]);
            exit;
        }

        try {
            $order = new Order(SessionManager::get('user_id'), $service->getTotal());
            foreach ($items as $item) {
                $order->addItem(new OrderItem($item['id'], $item['qty'], $item['price']));
            }

            $mapper = new OrderMapper();
            $mapper->save($order);
            $service->clear();

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
}