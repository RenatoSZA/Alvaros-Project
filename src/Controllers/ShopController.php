<?php
namespace App\Controllers;

use App\Mappers\ProductMapper;
use App\Services\CartService;
use Core\SessionManager;

class ShopController {

    public function index() {
        if (!SessionManager::isLogged()) {
            header('Location: /login');
            exit;
        }

        $mapper = new ProductMapper();
        $products = $mapper->findAll();
        
        echo "<h1>Loja da Academia</h1>";
        foreach($products as $p) {

            echo "<div style='border:1px solid #ccc; padding:10px; margin:5px;'>";
            echo "<h3>{$p->name}</h3>";
            echo "<p>R$ {$p->price}</p>";
            echo "<a href='/loja/add?id={$p->id}'>Adicionar ao Carrinho</a>";
            echo "</div>";
        }
        echo "<br><a href='/carrinho'>Ver Carrinho</a>";
    }

    public function addToCart() {
        $id = $_GET['id'] ?? null;
        if (!$id) die("Produto inválido");

        $mapper = new ProductMapper();
        $product = $mapper->find($id);

        if ($product) {
            $cartService = new CartService();
            $cartService->add($product->id, 1, $product->price, $product->name);
        }

        header('Location: /loja');
    }

    public function viewCart() {
        $cartService = new CartService();
        $items = $cartService->getItems();
        $total = $cartService->getTotal();

        echo "<h1>Seu Carrinho</h1>";
        if (empty($items)) {
            echo "<p>Vazio, frango!</p>";
        } else {
            foreach($items as $item) {
                echo "<li>{$item['qty']}x {$item['name']} - R$ " . ($item['price'] * $item['qty']) . "</li>";
            }
            echo "<h3>Total: R$ $total</h3>";
            echo "<button>Finalizar Compra (Checkout)</button>"; 
        }
        echo "<br><a href='/loja'>Voltar</a>";
    }
}