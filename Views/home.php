<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luminous Gym - Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/styles/style.css">
</head>

<body>

    <header class="header-hero-bg">
        <nav class="navbar container">
            <div class="logo"> <img src="<?= BASE_URL ?>/assets/img/Logo-black.png" alt="Luminous Gym"> </div>
            <ul class="nav-menu">
                <li><a href="#">Menu</a></li>
                <li><a href="#">Planos</a></li>
                <li><a href="#">Unidades</a></li>
                
                <?php if (\Core\SessionManager::isLogged()): ?>
                    <li><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
                    <li><a href="<?= BASE_URL ?>/loja">Loja</a></li>
                    <li><a href="<?= BASE_URL ?>/logout" style="font-weight:bold; color:var(--orange);">Sair</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>/login" style="font-weight:bold; color:var(--purple);">Entrar</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <section class="hero container">
            <div class="hero-text">
                <h1>Bem vindo, <br> à Luminous Gym</h1>
            </div>
            <div class="hero-inspiration">
                <h3>Seja a sua maior inspiração</h3>
                <p>Na Luminous Gym, acreditamos que cada treino é um passo rumo à sua melhor versão.</p>
            </div>
        </section>
    </header>

    <main>
        <section class="info-section container">
            <div class="info-text">
                <h2>Corpo em movimento, mente em equilíbrio</h2>
                <p>Na Luminous Gym, acreditamos que o bem-estar vai além do físico.</p>
            </div>
            <div class="info-image">
                <img src="<?= BASE_URL ?>/assets/img/home-m-remo.png" alt="Aluna praticando remo">
            </div>
        </section>

        <section class="info-section reverse container">
            <div class="info-text">
                <h2>Treine com os melhores</h2>
                <p>Nossa equipe de instrutores é composta por profissionais altamente qualificados.</p>
            </div>
            <div class="info-image">
                <img src="<?= BASE_URL ?>/assets/img/home-aluna-2.png" alt="Aluna treinando">
            </div>
        </section>

        <section class="plans-bg">
            <div class="plans container">
                <h2>Transforme sua rotina com <span class="highlight-pink">o plano perfeito pra você.</span></h2>
                <div class="plans-grid">
                    <div class="plan-card">
                        <h3>PLANO LUMINOUS (Premium)</h3>
                        <ul>
                            <li>✓ Acesso ilimitado</li>
                            <li>✓ Aulas exclusivas</li>
                        </ul>
                        <div class="price">R$ 169,90<span class="period">/mês</span></div>
                        <a href="#" class="btn btn-purple">Assine já</a>
                    </div>
                     <div class="plan-card">
                        <h3>PLANO BASE</h3>
                        <ul><li>✓ Acesso regular</li></ul>
                        <div class="price">R$ 99,90<span class="period">/mês</span></div>
                        <a href="#" class="btn btn-purple">Assine já</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="units container">
            <h2>Descubra a Luminous Gym</h2>
            <div class="units-grid">
                <div class="unit-card">
                    <img src="<?= BASE_URL ?>/assets/img/local-1.png" alt="Unidade Augusta">
                    <div class="unit-card-overlay">
                        <h3>Augusta</h3>
                        <a href="#" class="btn btn-terracotta">Conheça</a>
                    </div>
                </div>
                <div class="unit-card">
                    <img src="<?= BASE_URL ?>/assets/img/local-2.png" alt="Unidade Oscar Freire">
                    <div class="unit-card-overlay">
                        <h3>Oscar Freire</h3>
                        <a href="#" class="btn btn-terracotta">Conheça</a>
                    </div>
                </div>
                <div class="unit-card">
                    <img src="<?= BASE_URL ?>/assets/img/local-3.png" alt="Unidade A">
                    <div class="unit-card-overlay">
                        <h3>Unidade A</h3>
                        <a href="#" class="btn btn-terracotta">Conheça</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="shop-bg">
            <div class="shop container">
                <div class="shop-grid">
                    <div class="shop-item">
                        <img src="<?= BASE_URL ?>/assets/img/produto-3.png" alt="Stamina">
                        <h3>STAMINA</h3>
                        <div class="price">R$ 120,00</div>
                        <a href="#" class="btn btn-terracotta">Comprar</a>
                    </div>
                    <div class="shop-item">
                        <img src="<?= BASE_URL ?>/assets/img/produto-1.png" alt="Whey">
                        <h3>WHEY PROTEIN</h3>
                        <div class="price">R$ 180,00</div>
                        <a href="#" class="btn btn-terracotta">Comprar</a>
                    </div>
                    <div class="shop-item">
                        <img src="<?= BASE_URL ?>/assets/img/produto-2.png" alt="Whey Red">
                        <h3>WHEY RED</h3>
                        <div class="price">R$ 199,00</div>
                        <a href="#" class="btn btn-terracotta">Comprar</a>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer-bg">
             <div class="footer container">
                <div class="footer-column">
                    <div class="logo">Luminous Gym</div>
                    <p>Rua Exemplo, 123 - SP</p>
                </div>
                </div>
        </footer>

</body>
</html>