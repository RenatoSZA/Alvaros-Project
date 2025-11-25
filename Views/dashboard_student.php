<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Aluno - Luminous Gym</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/dashboardAluno.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <header class="dashboard-header">
        <div class="container header-content">
            <div class="header-left">
                <a href="<?= BASE_URL ?>/" class="back-link"><i data-lucide="arrow-left"></i> Voltar</a>
                <a href="<?= BASE_URL ?>/logout" class="back-link">Sair</a>
                <div class="welcome-text">
                    <h3 id="user-greeting">Olá, <?= htmlspecialchars($userName ?? 'Aluno') ?></h3>
                    <p id="current-date"><?= date('d/m/Y') ?></p>
                </div>
            </div>
            <div class="profile-pic" id="user-initials">
                <?= substr($userName ?? 'U', 0, 1) ?>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="training-panel">
            <h2>Seus Treinos</h2>
            <div class="week-grid" id="week-grid"></div>
        </section>
        
        </main>

    <script src="scripts/dashboardAluno.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>