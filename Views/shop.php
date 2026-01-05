<?php
use Core\SessionManager; 
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja - Luminous Gym</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/styles/shop.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'luminous-purple': '#8B5CF6',
                        'luminous-dark': '#111827',
                        'luminous-gray': '#1F2937',
                        'luminous-orange': '#F59E0B',
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-luminous-dark text-gray-100 font-sans antialiased">
    <div id="toast-container" class="fixed top-5 right-5 z-[100] flex flex-col gap-2"></div>

    <!-- Header Fixo -->
    <header class="sticky top-0 z-50 bg-luminous-dark/80 backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-4 h-20 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="<?= BASE_URL ?>/dashboard" class="p-2 hover:bg-white/5 rounded-full transition-colors">
                    <i data-lucide="arrow-left" class="w-6 h-6"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold leading-tight">Loja Luminous</h1>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold text-luminous-purple">Performance & Suplementos</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <button onclick="toggleCart()" class="relative p-3 bg-luminous-gray hover:bg-luminous-purple/20 rounded-xl transition-all border border-white/5 group">
                    <i data-lucide="shopping-bag" class="group-hover:text-luminous-purple transition-colors"></i>
                    <span id="cart-count" class="absolute -top-1 -right-1 bg-luminous-orange text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-luminous-dark hidden">0</span>
                </button>
                <div class="w-10 h-10 rounded-full bg-luminous-purple flex items-center justify-center font-bold text-sm shadow-lg shadow-luminous-purple/20">
                    <?= substr($userName ?? 'U', 0, 1) ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        
        <!-- Busca e Filtros -->
        <section class="max-w-4xl mx-auto mb-12">
            <div class="relative mb-6">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 w-5 h-5"></i>
                <input type="text" id="search-input" placeholder="O que seu corpo precisa hoje?" 
                       class="w-full bg-luminous-gray border-2 border-transparent focus:border-luminous-purple p-4 pl-12 rounded-2xl outline-none transition-all shadow-xl">
            </div>
            
            <div class="flex gap-3 overflow-x-auto pb-2 no-scrollbar scroll-smooth">
                <button class="filter-tag active px-6 py-2.5 rounded-full bg-luminous-gray border border-white/5 whitespace-nowrap transition-all font-semibold hover:border-luminous-purple" onclick="filterProducts('todos', this)">Todos</button>
                <button class="filter-tag px-6 py-2.5 rounded-full bg-luminous-gray border border-white/5 whitespace-nowrap transition-all font-semibold hover:border-luminous-purple" onclick="filterProducts('whey', this)">Proteínas</button>
                <button class="filter-tag px-6 py-2.5 rounded-full bg-luminous-gray border border-white/5 whitespace-nowrap transition-all font-semibold hover:border-luminous-purple" onclick="filterProducts('creatina', this)">Creatina</button>
                <button class="filter-tag px-6 py-2.5 rounded-full bg-luminous-gray border border-white/5 whitespace-nowrap transition-all font-semibold hover:border-luminous-purple" onclick="filterProducts('pre', this)">Pré-Treino</button>
            </div>
        </section>

        <!-- Seção: Destaques -->
        <section id="featured-section" class="mb-16">
            <div class="flex items-center gap-3 mb-8">
                <div class="p-2 bg-luminous-orange/10 rounded-lg">
                    <i data-lucide="star" class="text-luminous-orange w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold italic uppercase tracking-tighter">Em Destaque</h2>
                    <p class="text-gray-500 text-sm">Os favoritos da nossa comunidade</p>
                </div>
            </div>
            <div id="featured-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Skeletons dinâmicos aqui -->
            </div>
        </section>

        <!-- Banner Promocional -->
        <section class="mb-16 relative overflow-hidden bg-gradient-to-br from-luminous-purple/20 to-luminous-dark border border-luminous-purple/30 rounded-[2rem] p-8 md:p-12">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="max-w-xl text-center md:text-left">
                    <span class="inline-block bg-luminous-orange text-white text-[10px] font-black px-3 py-1 rounded-full mb-4">OFERTA EXCLUSIVA</span>
                    <h2 class="text-4xl md:text-5xl font-black mb-4 leading-none">COMBO HYPER-MASS</h2>
                    <p class="text-gray-300 text-lg mb-8">A combinação perfeita de Whey Isolado + Creatina para explodir seus ganhos.</p>
                    <button class="px-8 py-4 bg-luminous-purple hover:bg-luminous-purple/80 text-white font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl shadow-luminous-purple/20">
                        Resgatar Desconto (25% OFF)
                    </button>
                </div>
                <div class="w-64 h-64 relative">
                    <div class="absolute inset-0 bg-luminous-purple blur-[80px] opacity-20 animate-pulse"></div>
                    <img src="<?= BASE_URL ?>/assets/img/produto-2.png" alt="Promo" class="relative z-20 drop-shadow-[0_20px_50px_rgba(139,92,246,0.5)]">
                </div>
            </div>
        </section>

        <!-- Catálogo -->
        <section class="mb-12">
            <div class="mb-8">
                <h2 class="text-2xl font-extrabold italic uppercase tracking-tighter">Catálogo Completo</h2>
                <p class="text-gray-500 text-sm">Qualidade premium em cada dose</p>
            </div>
            <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Injetado via JS -->
            </div>
        </section>
    </main>

    <!-- Carrinho Lateral -->
    <div id="cart-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] opacity-0 pointer-events-none transition-opacity duration-300" onclick="toggleCart()"></div>
    <aside id="cart-sidebar" class="fixed right-0 top-0 h-full w-full max-w-md bg-luminous-gray z-[70] translate-x-full transition-transform duration-500 ease-in-out flex flex-col shadow-2xl border-l border-white/5">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i data-lucide="shopping-cart" class="text-luminous-purple"></i>
                <h3 class="font-bold text-lg">Seu Carrinho</h3>
            </div>
            <button onclick="toggleCart()" class="p-2 hover:bg-white/5 rounded-lg transition-colors">
                <i data-lucide="x"></i>
            </button>
        </div>
        
        <div id="cart-items" class="flex-1 overflow-y-auto p-6 space-y-4">
            <!-- Items via JS -->
        </div>

        <div id="cart-footer" class="p-6 bg-luminous-dark border-t border-white/5 hidden">
            <div class="flex justify-between items-center mb-6">
                <span class="text-gray-400 font-semibold uppercase text-xs">Valor Total</span>
                <span id="cart-total" class="text-2xl font-black text-white">R$ 0,00</span>
            </div>
            <button onclick="checkout()" class="w-full py-5 bg-luminous-purple hover:bg-luminous-purple/90 text-white font-black rounded-2xl transition-all shadow-xl shadow-luminous-purple/10 flex items-center justify-center gap-2">
                FINALIZAR COMPRA <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </button>
        </div>
    </aside>

    <script>const BASE_URL = "<?= BASE_URL ?>";</script>
    <script src="<?= BASE_URL ?>/scripts/shop.js"></script>
    <script>lucide.createIcons();</script>
</body>
</html>