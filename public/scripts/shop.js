let allProducts = [];
let cart = [];

document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
    fetchCart();
    lucide.createIcons();
    
    // Search listener
    // O input de busca foi movido para dentro da Hero Section
    document.getElementById('search-input').addEventListener('input', () => {
        renderProducts(); // Renderiza usando o termo de busca E o filtro ativo
    });
    
    // Adiciona listener para os botões de filtro (tags na Hero)
    document.querySelectorAll('.search-filters .filter-tag').forEach(btn => {
        btn.addEventListener('click', () => filterProducts(btn.innerText.toLowerCase(), btn));
    });
});

// --- API Calls ---

async function fetchProducts() {
    try {
        const res = await fetch(`${BASE_URL}/api/products`);
        const data = await res.json();
        allProducts = data;
        renderProducts();
    } catch (error) {
        console.error("Erro ao buscar produtos:", error);
        document.getElementById('products-grid').innerHTML = '<p>Erro ao carregar loja.</p>';
    }
}

async function fetchCart() {
    try {
        const res = await fetch(`${BASE_URL}/api/cart`);
        const data = await res.json();
        // O backend retorna um objeto onde as chaves são IDs, precisamos transformar em array
        cart = Object.values(data);
        updateCartUI();
    } catch (error) {
        console.error("Erro ao buscar carrinho:", error);
    }
}

async function addToCart(id) {
    try {
        // CORREÇÃO: Usando encodeURIComponent para garantir IDs com espaços/caracteres especiais (embora IDs sejam inteiros, é boa prática para URLs)
        const res = await fetch(`${BASE_URL}/api/cart/add?id=${encodeURIComponent(id)}`);
        const data = await res.json();
        if (data.success) {
            fetchCart(); // Recarrega o carrinho atualizado
            toggleCart(true); // Abre o carrinho
        } else {
            alert(`Erro ao adicionar produto: ${data.message || 'Desconhecido'}`);
        }
    } catch (error) {
        alert("Erro ao adicionar produto.");
        console.error("Erro na chamada addToCart:", error);
    }
}

async function updateQty(id, action) {
    // action: 'add' ou 'remove'
    
    if (action === 'add') {
        await addToCart(id); // Reusa o endpoint de add para incrementar
    } else {
        try {
            // CORREÇÃO: Usando encodeURIComponent
            const res = await fetch(`${BASE_URL}/api/cart/remove?id=${encodeURIComponent(id)}`);
            const data = await res.json();
            if(data.success) {
                fetchCart();
            } else {
                console.error("Erro ao remover: ", data.message);
            }
        } catch (error) {
            console.error(error);
            alert("Erro de conexão ao remover.");
        }
    }
}

async function checkout() {
    // Usando uma modal customizada para confirmação (boa prática)
    const confirmation = confirm("Deseja finalizar a compra? (Simulação)"); 
    
    if(confirmation) {
        try {
            const btn = document.querySelector('.cart-footer .btn-primary');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="loader-2" class="animate-spin" width="18"></i> Finalizando...';
            lucide.createIcons();

            const res = await fetch(`${BASE_URL}/api/cart/checkout`, { method: 'POST' });
            const data = await res.json();

            btn.innerHTML = originalText;
            lucide.createIcons();
            
            if(data.success) {
                alert("Pedido realizado com sucesso! ID: " + data.orderId);
                cart = [];
                updateCartUI();
                toggleCart(false);
            } else {
                alert("Erro ao finalizar: " + data.message);
            }
        } catch (e) {
            alert("Erro de conexão.");
            console.error(e);
        }
    }
}

// --- UI Functions ---

function renderProducts() {
    const grid = document.getElementById('products-grid');
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    
    grid.innerHTML = '';
    
    // Determina o filtro ativo (usando o novo seletor .filter-tag)
    const activeBtn = document.querySelector('.search-filters .filter-tag.active');
    let activeCategory = activeBtn && activeBtn.innerText !== 'Todos' ? activeBtn.innerText.toLowerCase() : '';

    // Lógica para mapear categorias de tags para o filtro (ex: Whey Protein -> whey)
    const categoryMap = {
        'whey protein': 'whey',
        'creatina': 'creatina',
        'shakes': 'shakes',
        'suplementos': 'suplementos', // Se houver no DB
        'roupas': 'roupas', // Se houver no DB
    };
    
    // Obtém o termo de busca simplificado da categoria
    const simpleCategoryTerm = categoryMap[activeCategory] || activeCategory;


    let filtered = allProducts.filter(p => {
        const matchesSearch = p.name.toLowerCase().includes(searchTerm) || 
                              (p.description && p.description.toLowerCase().includes(searchTerm));
                              
        let matchesCategory = true;
        
        if (simpleCategoryTerm) {
            // Se houver uma categoria ativa (não "todos"), verifica se o nome ou descrição contém o termo da categoria
            // Isso é um MOCK de filtragem, pois não temos uma coluna 'category' no DB
            matchesCategory = p.name.toLowerCase().includes(simpleCategoryTerm) || 
                              (p.description && p.description.toLowerCase().includes(simpleCategoryTerm));
        }

        return matchesSearch && matchesCategory;
    });

    if (filtered.length === 0) {
        grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; color:var(--gray); padding:3rem;">Nenhum produto encontrado. Tente redefinir os filtros.</div>';
        return;
    }

    filtered.forEach(p => {
        // Fallback de imagem
        // CORREÇÃO: p.image_url agora vem do mapper, usando a URL do DB ou o fallback.
        const imgUrl = p.image_url && p.image_url.trim() !== '' ? p.image_url : `${BASE_URL}/assets/img/produto-1.png`;
        
        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
            <div class="product-img-wrapper">
                <!-- Usando o image_url recuperado do banco/mapper -->
                <img src="${imgUrl}" alt="${p.name}" onerror="this.src='${BASE_URL}/assets/img/produto-1.png'">
            </div>
            <div class="product-info">
                <span class="product-cat">Luminous Gear</span>
                <h3 class="product-title">${p.name}</h3>
                <div class="product-price">R$ ${parseFloat(p.price).toFixed(2).replace('.', ',')}</div>
                <button class="btn btn-primary btn-add-cart" onclick="addToCart(${p.id})">
                    <i data-lucide="shopping-bag" width="18"></i> Adicionar
                </button>
            </div>
        `;
        grid.appendChild(card);
    });
    
    lucide.createIcons();
}

function updateCartUI() {
    const container = document.getElementById('cart-items');
    const footer = document.getElementById('cart-footer');
    const badge = document.getElementById('cart-count');
    
    container.innerHTML = '';
    
    let total = 0;
    let count = 0;

    if (cart.length === 0) {
        container.innerHTML = `
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; color:var(--gray);">
                <i data-lucide="shopping-cart" size="48" style="margin-bottom:1rem; opacity:0.5"></i>
                <p>Seu carrinho está vazio.</p>
            </div>
        `;
        footer.style.display = 'none';
        badge.style.display = 'none';
    } else {
        cart.forEach(item => {
            // CORREÇÃO: Garante que os valores sejam numéricos
            const itemPrice = parseFloat(item.price);
            const itemQty = parseInt(item.qty);

            total += itemPrice * itemQty;
            count += itemQty;
            
            // Tenta achar imagem no allProducts, senão fallback
            const productInfo = allProducts.find(p => p.id == item.id);
            // Agora usa o image_url retornado do servidor
            const imgUrl = productInfo && productInfo.image_url && productInfo.image_url.trim() !== '' 
                           ? productInfo.image_url 
                           : `${BASE_URL}/assets/img/produto-1.png`;

            const itemEl = document.createElement('div');
            itemEl.className = 'cart-item';
            itemEl.innerHTML = `
                <!-- Usando a imagem correta do produto no carrinho -->
                <img src="${imgUrl}" class="cart-item-img" onerror="this.src='${BASE_URL}/assets/img/produto-1.png'">
                <div class="cart-item-info">
                    <div class="cart-item-title">${item.name}</div>
                    <div class="cart-item-price">R$ ${itemPrice.toFixed(2).replace('.', ',')}</div>
                    <div class="cart-controls">
                        <!-- ID é passado aqui para a função updateQty -->
                        <button class="qty-btn" onclick="updateQty(${item.id}, 'remove')"><i data-lucide="minus" width="14"></i></button>
                        <span style="font-weight:bold; min-width:20px; text-align:center;">${itemQty}</span>
                        <button class="qty-btn" onclick="updateQty(${item.id}, 'add')"><i data-lucide="plus" width="14"></i></button>
                    </div>
                </div>
            `;
            container.appendChild(itemEl);
        });

        document.getElementById('cart-subtotal').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
        document.getElementById('cart-total').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
        footer.style.display = 'block';
        
        badge.innerText = count;
        badge.style.display = 'flex';
    }
    
    lucide.createIcons();
}

function toggleCart(forceOpen = null) {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    
    if (forceOpen === true) {
        sidebar.classList.add('open');
        overlay.classList.add('active');
    } else if (forceOpen === false) {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    } else {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    }
}

// ATUALIZADO para usar as novas tags de filtro
function filterProducts(category, btn) {
    // UI Update: Remove active de todos e adiciona no clicado
    document.querySelectorAll('.search-filters .filter-tag').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    // O termo de busca do input já é lido dentro de renderProducts
    renderProducts();
}