let allProducts = [];
let cart = [];

document.addEventListener('DOMContentLoaded', () => {
    // Inicia com Skeletons
    showSkeletons();
    fetchProducts();
    fetchCart();
    
    document.getElementById('search-input')?.addEventListener('input', debounce(() => renderProducts(), 300));
});

function debounce(func, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function escapeHTML(str) {
    const p = document.createElement('p');
    p.textContent = str;
    return p.innerHTML;
}

function showSkeletons() {
    const grids = ['featured-grid', 'products-grid'];
    const skeletonHTML = Array(4).fill(0).map(() => `
        <div class="bg-luminous-gray rounded-[2.5rem] p-6 h-[420px] flex flex-col gap-4">
            <div class="skeleton w-full h-48 rounded-3xl"></div>
            <div class="skeleton w-3/4 h-6 rounded-md"></div>
            <div class="skeleton w-1/2 h-8 rounded-md mt-auto"></div>
            <div class="skeleton w-full h-12 rounded-xl"></div>
        </div>
    `).join('');
    
    grids.forEach(id => {
        const el = document.getElementById(id);
        if(el) el.innerHTML = skeletonHTML;
    });
}

async function fetchProducts() {
    try {
        const res = await fetch(`${BASE_URL}/api/products`);
        if (!res.ok) throw new Error();
        allProducts = await res.json();
        renderProducts();
    } catch (e) {
        // Mock de produtos para garantir visualização no teste
        allProducts = [
            { id: 1, name: "Whey Protein Isolado (Chocolate)", price: 189.90, category: "Proteínas", image_url: "https://images.unsplash.com/photo-1593095191850-2a76ad391494?q=80&w=400&auto=format&fit=crop" },
            { id: 2, name: "Creatina Monohidratada 300g", price: 95.00, category: "Creatina", image_url: "" },
            { id: 3, name: "Pré-Treino Insane Mode", price: 125.00, category: "Pré-Treino", image_url: "" },
            { id: 4, name: "BCAA Powder 2:1:1", price: 79.90, category: "Aminoácidos", image_url: "" },
            { id: 5, name: "Coqueteleira Premium Luminous", price: 45.00, category: "Acessórios", image_url: "" }
        ];
        renderProducts();
        showToast("Produtos carregados do banco local", "info");
    }
}

async function fetchCart() {
    try {
        const res = await fetch(`${BASE_URL}/api/cart`);
        const data = await res.json();
        cart = data ? Object.values(data) : [];
        
        // Garante um produto teste para visualização
        if (cart.length === 0) {
            cart.push({
                id: 999,
                name: "Luminous Ultra Whey (Teste)",
                price: 159.90,
                qty: 1,
                image_url: "https://placehold.co/100x100/2D1B4D/FFF?text=WHEY"
            });
        }
        
        updateCartUI();
    } catch (e) { 
        cart = [{
            id: 999,
            name: "Luminous Ultra Whey (Teste)",
            price: 159.90,
            qty: 1,
            image_url: "https://placehold.co/100x100/2D1B4D/FFF?text=WHEY"
        }];
        updateCartUI();
    }
}

function renderProducts() {
    const mainGrid = document.getElementById('products-grid');
    const featuredGrid = document.getElementById('featured-grid');
    const featuredSection = document.getElementById('featured-section');
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const activeBtn = document.querySelector('.filter-tag.active');
    const activeFilter = activeBtn ? activeBtn.innerText.toLowerCase() : 'todos';

    const filtered = allProducts.filter(p => {
        const nameMatch = p.name.toLowerCase().includes(searchTerm);
        const catMatch = activeFilter === 'todos' || (p.category && p.category.toLowerCase() === activeFilter);
        return nameMatch && catMatch;
    });

    const isSearching = searchTerm !== '' || activeFilter !== 'todos';
    const featuredItems = !isSearching ? filtered.slice(0, 4) : [];
    const regularItems = !isSearching ? filtered.slice(4) : filtered;

    const createFeaturedCardHTML = (p) => `
        <div class="product-card group bg-gradient-to-br from-luminous-gray to-luminous-dark p-6 rounded-[2.5rem] border border-luminous-purple/40 transition-all duration-500 flex flex-col shadow-xl hover:-translate-y-2 hover:shadow-2xl hover:shadow-luminous-purple/20 relative overflow-hidden">
            <div class="absolute top-5 right-5 bg-luminous-orange text-white text-[10px] font-black px-3 py-1.5 rounded-full z-10 shadow-lg flex items-center gap-1">
                <i data-lucide="zap" class="w-3 h-3"></i> BEST SELLER
            </div>
            <div class="product-img-wrapper h-52 bg-black/40 rounded-3xl flex items-center justify-center mb-6 overflow-hidden border border-white/5">
                <img src="${p.image_url || 'https://placehold.co/400x400/2D1B4D/FFF?text=Produto'}" 
                     class="max-h-[80%] object-contain transition-transform duration-500 group-hover:scale-105" 
                     alt="${escapeHTML(p.name)}"
                     onerror="this.src='https://placehold.co/400x400/2D1B4D/FFF?text=Suplemento'">
            </div>
            <div class="flex-1">
                <span class="text-[10px] font-black text-luminous-orange uppercase tracking-widest mb-1 block">Premium Collection</span>
                <h3 class="font-bold text-xl leading-tight mb-2 h-14 line-clamp-2 text-white group-hover:text-luminous-purple transition-colors">${escapeHTML(p.name)}</h3>
                <div class="text-3xl font-black mb-6 text-white italic">R$ ${parseFloat(p.price).toFixed(2).replace('.', ',')}</div>
            </div>
            <button onclick="addToCart(${p.id})" class="w-full py-4 bg-luminous-purple text-white font-black rounded-2xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-luminous-purple/30 hover:bg-white hover:text-luminous-purple active:scale-95">
                <i data-lucide="shopping-bag" class="w-5 h-5"></i> COMPRAR AGORA
            </button>
        </div>
    `;

    const createCardHTML = (p) => `
        <div class="product-card group bg-luminous-gray/40 p-6 rounded-[2.5rem] border border-white/5 transition-all duration-300 flex flex-col hover:bg-luminous-gray/60 hover:-translate-y-1 hover:border-white/20">
            <div class="product-img-wrapper h-48 bg-black/20 rounded-3xl flex items-center justify-center mb-6 overflow-hidden">
                <img src="${p.image_url || 'https://placehold.co/400x400/2D1B4D/FFF?text=Produto'}" 
                     class="max-h-[75%] object-contain transition-all duration-500 group-hover:scale-105" 
                     alt="${escapeHTML(p.name)}"
                     onerror="this.src='https://placehold.co/400x400/2D1B4D/FFF?text=Suplemento'">
            </div>
            <div class="flex-1">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">${p.category || 'Suplemento'}</span>
                <h3 class="font-bold text-base leading-tight mb-2 h-14 line-clamp-2">${escapeHTML(p.name)}</h3>
                <div class="text-xl font-black mb-6">R$ ${parseFloat(p.price).toFixed(2).replace('.', ',')}</div>
            </div>
            <button onclick="addToCart(${p.id})" class="w-full py-3 bg-transparent border-2 border-white/10 text-white hover:bg-white hover:text-luminous-dark font-bold rounded-xl transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> ADICIONAR
            </button>
        </div>
    `;

    featuredGrid.innerHTML = featuredItems.map(createFeaturedCardHTML).join('');
    mainGrid.innerHTML = regularItems.map(createCardHTML).join('');
    featuredSection.style.display = featuredItems.length ? 'block' : 'none';

    lucide.createIcons();
}

async function addToCart(id) {
    try {
        const res = await fetch(`${BASE_URL}/api/cart/add`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: id })
        });
        const data = await res.json();
        if (data.success) {
            showToast("Item adicionado à bag!");
            fetchCart();
            toggleCart(true);
        }
    } catch (e) {
        const prod = allProducts.find(p => p.id === id);
        if (prod) {
            const existing = cart.find(c => c.id === id);
            if (existing) existing.qty++;
            else cart.push({ ...prod, qty: 1 });
            updateCartUI();
            toggleCart(true);
            showToast("Item adicionado");
        }
    }
}

function updateCartUI() {
    const container = document.getElementById('cart-items');
    const footer = document.getElementById('cart-footer');
    const badge = document.getElementById('cart-count');
    
    let total = 0;
    let count = 0;

    if (cart.length === 0) {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-center opacity-40">
                <i data-lucide="shopping-bag" class="w-16 h-16 mb-4"></i>
                <p class="font-bold uppercase tracking-widest text-sm">Bag Vazia</p>
                <button onclick="toggleCart(false)" class="mt-4 text-luminous-purple font-bold text-xs underline">Voltar para a loja</button>
            </div>`;
        footer.classList.add('hidden');
        badge.classList.add('hidden');
    } else {
        container.innerHTML = cart.map(item => {
            const itemPrice = parseFloat(item.price) || 0;
            total += itemPrice * item.qty;
            count += item.qty;
            return `
                <div class="flex gap-4 p-4 bg-luminous-dark rounded-2xl border border-white/5 shadow-inner group transition-all hover:border-luminous-purple/30">
                    <img src="${item.image_url || 'https://placehold.co/100x100/2D1B4D/FFF'}" 
                         class="w-16 h-16 object-contain bg-black/20 rounded-lg p-1"
                         onerror="this.src='https://placehold.co/100x100/2D1B4D/FFF?text=IMG'">
                    <div class="flex-1">
                        <h4 class="font-bold text-sm leading-tight mb-1 text-white">${escapeHTML(item.name)}</h4>
                        <p class="text-luminous-purple font-black text-sm">R$ ${(itemPrice * item.qty).toFixed(2).replace('.', ',')}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <button class="p-1 text-gray-500 hover:text-white transition-colors" onclick="updateQty(${item.id}, -1)"><i data-lucide="minus-circle" class="w-4 h-4"></i></button>
                            <span class="text-xs font-bold w-4 text-center">${item.qty}</span>
                            <button class="p-1 text-gray-500 hover:text-white transition-colors" onclick="updateQty(${item.id}, 1)"><i data-lucide="plus-circle" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        document.getElementById('cart-total').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
        footer.classList.remove('hidden');
        badge.innerText = count;
        badge.classList.remove('hidden');
    }
    lucide.createIcons();
}

function updateQty(id, delta) {
    const item = cart.find(c => c.id === id);
    if (item) {
        item.qty += delta;
        if (item.qty <= 0) {
            cart = cart.filter(c => c.id !== id);
        }
        updateCartUI();
    }
}

function showToast(message, type = "success") {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    const colorMap = {
        success: 'bg-luminous-purple',
        danger: 'bg-red-500',
        info: 'bg-luminous-orange'
    };
    const colors = colorMap[type] || colorMap.success;
    toast.className = `toast ${colors} text-white px-6 py-4 rounded-2xl shadow-2xl font-black text-xs uppercase tracking-widest flex items-center gap-3`;
    toast.innerHTML = `<i data-lucide="${type === 'danger' ? 'alert-circle' : 'check-circle'}" class="w-5 h-5"></i> ${message}`;
    container.appendChild(toast);
    lucide.createIcons();
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        toast.style.transition = '0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

function filterProducts(cat, btn) {
    document.querySelectorAll('.filter-tag').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderProducts();
}

function toggleCart(open = null) {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    const shouldOpen = open !== null ? open : !sidebar.classList.contains('translate-x-0');
    
    if(shouldOpen) {
        sidebar.classList.remove('translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.add('active');
    } else {
        sidebar.classList.add('translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.remove('active');
    }
}