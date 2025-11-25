<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Luminous Gym</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/login.css">
</head>

<body>
    <div class="login-wrapper">
        <div class="login-form-container">
            <div class="login-content">
                <h1>Login</h1>

                <?php if (isset($error)): ?>
                    <div style="color: red; margin-bottom: 1rem; font-weight: bold;">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['registered'])): ?>
                    <div style="color: green; margin-bottom: 1rem; font-weight: bold;">
                        Cadastro realizado! Entre agora.
                    </div>
                <?php endif; ?>

                <form id="login-form" action="<?= BASE_URL ?>/login" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="ex: aluno@gym.com" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>

                    <button type="submit" class="btn-login">Entrar</button>
                </form>

                <div class="social-login">
                    <div class="social-icons-container">
                        <a href="#" title="Login com Google"><img src="assets/img/icon-google-logo.png" alt="google"></a>
                        <a href="#" title="Login com Facebook"><img src="assets/img/icon-facebook-logo.png" alt="Facebook"></a>
                        <a href="#" title="Login com Apple"><img src="assets/img/icon-apple-logo.png" alt="apple"></a>
                    </div>
                </div>

                <footer class="login-footer-text">
                    <p>Ainda não tem conta? <a href="<?= BASE_URL ?>/register" style="color:var(--light-purple); text-decoration:none; font-weight:700;">Cadastre-se</a></p>
                    <p class="caption">Plataforma exclusiva para membros cadastrados.</p>
                </footer>
            </div>
        </div>
        <div class="login-image-container">
            <div class="blob-shape">
                <h2>Bem vindo <br> de volta!</h2>
            </div>
        </div>
    </div>
</body>

</html>