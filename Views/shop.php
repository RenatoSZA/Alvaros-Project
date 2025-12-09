<?php
// Certifique-se de iniciar a sessão ou importar o SessionManager no index.php
use Core\SessionManager; 
// A variável $userName é definida no ShopController
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja - Luminous Gym</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Dependências CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/styles/dashboardAluno.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/styles/shop.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>

    <!-- Header (Mesmo do Dashboard) -->
    <header class="dashboard-header">
        <div class="container header-content">
            <div class="header-left">
                <a href="<?= BASE_URL ?>/dashboard" class="back-link"><i data-lucide="arrow-left"></i> Voltar ao Dashboard</a>
            </div>
            
            <div style="display: flex; gap: 1.5rem; align-items: center;">
                <button class="btn-icon" style="position: relative;" onclick="toggleCart()">
                    <i data-lucide="shopping-cart"></i>
                    <span id="cart-count" class="cart-badge" style="display: none;">0</span>
                </button>
                <div class="profile-pic">
                    <?= substr($userName ?? 'U', 0, 1) ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container shop-container">
        
        <!-- Hero Section (Novo Design - Baseado no Protótipo) -->
        <section class="shop-hero">
            <div class="hero-content">
                <h1>Suplementos e Equipamentos</h1>
                <p>Potencialize seus resultados com os melhores produtos do mercado selecionados por nossos especialistas.</p>
                
                <div class="search-and-filters">
                    <span class="filter-label" style="display:block; margin-bottom: 0.5rem; color: var(--light-purple); font-weight:700;">Encontre o suplemento ideal para você</span>
                    
                    <div class="search-filters">
                        <!-- Tags de filtro (serão usadas pelo JS) -->
                        <button class="filter-tag active" onclick="filterProducts('todos', this)">Todos</button>
                        <button class="filter-tag" onclick="filterProducts('whey', this)">Whey Protein</button>
                        <button class="filter-tag" onclick="filterProducts('creatina', this)">Creatina</button>
                        <button class="filter-tag" onclick="filterProducts('shakes', this)">Shakes</button>
                    </div>
                    
                    <!-- Busca por texto -->
                    <div class="search-bar" style="margin-top: 1rem;">
                        <i data-lucide="search"></i>
                        <input type="text" id="search-input" placeholder="Buscar produtos...">
                    </div>
                </div>
            </div>
            
            <!-- Imagem de Produto no Hero (Placeholder) -->
            <div class="hero-img-wrapper">
                <img src="<?= BASE_URL ?>/assets/img/produto-1.png" alt="Suplemento Whey">
            </div>
        </section>
        
        <!-- Promo Banner (Baseado no Protótipo) -->
        <section class="promo-banner">
            <div class="promo-content">
                <div class="promo-img-wrapper">
                    <img src="<?= BASE_URL ?>/assets/img/produto-2.png" alt="Creatina em Promoção">
                </div>
                <div class="promo-text">
                    <h2>MEGA PROMOÇÃO</h2>
                    <h3>45% OFF em creatina</h3>
                    <p>Somente até o final da semana!</p>
                </div>
            </div>
        </section>

        <!-- Div de Título para a Grid de Produtos -->
        <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 1.5rem; color: var(--white);">Catálogo Completo</h2>

        <!-- Grid de Produtos (Aqui serão injetados os cards de produto pelo JS) -->
        <div id="products-grid" class="products-grid">
            <!-- Renderizado via JS -->
            <div style="grid-column: 1/-1; text-align: center; padding: 4rem;">
                <p style="color: var(--gray);">Carregando produtos...</p>
            </div>
        </div>

    </main>

    <!-- Carrinho Lateral -->
    <div id="cart-overlay" class="overlay" onclick="toggleCart()"></div>
    
    <aside id="cart-sidebar" class="cart-sidebar">
        <div class="cart-header">
            <h3><i data-lucide="shopping-bag" style="vertical-align: middle; margin-right: 0.5rem; color: var(--purple);"></i> Seu Carrinho</h3>
            <button class="btn-icon" onclick="toggleCart()"><i data-lucide="x"></i></button>
        </div>

        <div id="cart-items" class="cart-body">
            <!-- Renderizado via JS -->
            <div style="text-align: center; color: var(--gray); margin-top: 2rem;">
                Seu carrinho está vazio.
            </div>
        </div>

        <div class="cart-footer" id="cart-footer" style="display: none;">
            <div class="cart-summary-row">
                <span>Subtotal</span>
                <span id="cart-subtotal">R$ 0,00</span>
            </div>
            <div class="cart-total">
                <span>Total</span>
                <span id="cart-total" style="color: var(--purple);">R$ 0,00</span>
            </div>
            <button class="btn btn-primary" style="width: 100%; justify-content: center;" onclick="checkout()">
                Finalizar Compra <i data-lucide="arrow-right"></i>
            </button>
        </div>
    </aside>

    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
    <!-- Script da Loja -->
    <script src="<?= BASE_URL ?>/scripts/shop.js"></script>
    <script>
        // Re-executa os ícones do lucide após o carregamento da página
        lucide.createIcons();
    </script>
</body>
</html>