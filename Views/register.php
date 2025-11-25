<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Luminous Gym</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="styles/cadastro.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="cadastro-wrapper">
        <main class="form-container">
            <div class="form-content">
                <a href="/" class="logo-link">
                    <span style="font-weight:800; font-size:1.2rem; color:white;">Luminous Gym</span>
                </a>

                <h2>Sua jornada começa aqui</h2>

                <?php if (isset($error)): ?>
                    <p style="color:red; font-weight:bold;"><?= $error ?></p>
                <?php endif; ?>

                <p class="form-instruction">Preencha os dados abaixo</p>

                <form id="cadastro-form" action="<?= BASE_URL ?>/register" method="POST">
                    <div class="form-group">
                        <label for="nome">Nome completo</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="telefone">Numero do telefone</label>
                        <input type="tel" id="telefone" name="telefone" placeholder="+00 (00) 00000-0000">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="senha">Crie uma Senha</label>
                        <input type="password" id="senha" name="senha" required placeholder="Mínimo 6 caracteres">
                    </div>

                    <div class="form-group">
                        <label for="cep">CEP</label>
                        <input type="text" id="cep" name="cep" placeholder="00000-000">
                    </div>

                    <button type="submit" class="btn-cadastrar">Cadastrar</button>
                </form>

                <div class="form-footer">
                    <div class="terms">
                        <input type="checkbox" id="termos" name="termos" required>
                        <label for="termos">Li e aceito os termos.</label>
                    </div>

                    <p class="login-link">Já possui conta? <a href="<?= BASE_URL ?>/login">Entrar</a></p>
                </div>
            </div>
        </main>
        <aside class="image-container">
            <div class="image-grid">
                <div class="grid-panel"><img src="assets/img/cad-img1.png" alt=""><span class="overlay-text">VOCÊ</span></div>
                <div class="grid-panel"><img src="assets/img/cad-img2.png" alt=""><span class="overlay-text">FAZ</span></div>
                <div class="grid-panel"><img src="assets/img/cad-img3.png" alt=""><span class="overlay-text">ACONTECER</span></div>
            </div>
        </aside>
    </div>
</body>

</html>