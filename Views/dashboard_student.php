<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Aluno - Luminous Gym</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/styles/dashboardAluno.css">

    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>

    <header class="dashboard-header">
        <div class="container header-content">
            <div class="header-left">
                <a href="<?= BASE_URL ?>/" class="back-link"><i data-lucide="arrow-left"></i> Voltar</a>
                <a href="<?= BASE_URL ?>/logout" class="back-link"><i data-lucide="log-out"></i> Sair</a>
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

        <section class="progress-section">
            <div class="section-card">
                <h4>Progresso Semanal</h4>
                <div class="progress-item">
                    <div class="progress-label"><span>Frequência</span> <span id="prog-freq-val">0/0</span></div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" id="prog-freq-bar"></div>
                    </div>
                </div>
                <button class="btn btn-primary" onclick="window.print()" style="width:100%; margin-top:1rem;">
                    <i data-lucide="printer"></i> Imprimir Relatório
                </button>
            </div>

            <div class="section-card">
                <h4>Metas & Performance</h4>
                <div class="charts-grid">
                    <div class="chart-container">
                        <p style="color:var(--gray)">Calorias</p>
                        <div class="chart-donut" id="chart-cal" style="--p: 0;"><span class="chart-value" id="val-cal">0%</span></div>
                    </div>
                    <div class="chart-container">
                        <p style="color:var(--gray)">Cardio</p>
                        <div class="chart-donut" id="chart-cardio" style="--p: 0;"><span class="chart-value" id="val-cardio">0%</span></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="modal-overlay" id="modal-list">
        <div class="modal-list-content">
            <div style="padding:1.5rem; border-bottom:1px solid var(--medium-gray); display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 id="list-title" style="font-size:1.5rem; color:var(--white)">Treino</h3>
                    <span id="list-subtitle" style="color:var(--purple)">Detalhes</span>
                </div>
                <button class="btn-icon" onclick="closeListModal()"><i data-lucide="x"></i></button>
            </div>

            <div style="padding:1.5rem 1.5rem 0 1.5rem;">
                <button class="btn btn-primary" style="width:100%" onclick="startWorkout()">
                    <i data-lucide="play-circle"></i> INICIAR TREINO AGORA
                </button>
            </div>

            <div style="padding:1.5rem; overflow-y:auto;">
                <table class="details-table">
                    <thead>
                        <tr>
                            <th>Exercício</th>
                            <th>Séries</th>
                            <th>Reps</th>
                            <th>Carga</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="list-body"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modal-execution">
        <div class="modal-exec-content">
            <div class="exec-progress">
                <div class="exec-progress-bar" id="exec-progress-bar"></div>
            </div>

            <div class="exec-header">
                <div>
                    <span style="font-size:0.8rem; color:var(--gray); text-transform:uppercase;">Exercício <span id="exec-step">1</span> de <span id="exec-total">5</span></span>
                    <h3 id="exec-name" style="font-size:1.2rem; color:var(--white)">Exercício</h3>
                </div>
                <button class="btn-icon" onclick="closeExecutionModal()"><i data-lucide="x"></i></button>
            </div>

            <div class="exec-body">
                <div class="exec-image">
                    <img id="exec-img" src="" alt="Ilustração">
                    <div id="exec-img-placeholder" style="color:var(--gray); display:none;"><i data-lucide="image" size="48"></i></div>
                </div>

                <div class="exec-stats">
                    <div class="stat-box"><span class="stat-label">Séries</span><span class="stat-val" id="exec-sets">-</span></div>
                    <div class="stat-box"><span class="stat-label">Reps</span><span class="stat-val" id="exec-reps">-</span></div>
                    <div class="stat-box"><span class="stat-label">Carga</span><span class="stat-val" id="exec-load">-</span></div>
                </div>

                <div class="exec-notes">
                    <h6><i data-lucide="info" size="14"></i> Observações:</h6>
                    <p id="exec-note">...</p>
                </div>

                <div class="exec-timer">
                    <span style="display:block; font-size:0.8rem; color:var(--gray); margin-bottom:0.5rem;">TIMER DE DESCANSO</span>
                    <div class="timer-display" id="timer-val">00:00</div>
                    <div class="timer-controls">
                        <button class="btn btn-outline" onclick="resetTimer()"><i data-lucide="rotate-ccw"></i></button>
                        <button class="btn btn-primary" id="btn-timer-toggle" onclick="toggleTimer()"><i data-lucide="play"></i> Iniciar</button>
                    </div>
                </div>
            </div>

            <div class="exec-footer">
                <button class="btn btn-primary" style="width:100%; background:var(--green); color:var(--black)" onclick="finishSet()">
                    <i data-lucide="check-circle"></i> CONCLUIR SÉRIE
                </button>

                <div class="nav-controls">
                    <button class="nav-btn" onclick="prevExercise()"><i data-lucide="chevron-left"></i> Anterior</button>
                    <button class="nav-btn" onclick="skipExercise()">Pular <i data-lucide="skip-forward"></i></button>
                </div>
            </div>
        </div>
    </div>

    <footer class="dashboard-footer" style="background-color:var(--dark-gray); padding:2rem; text-align:center; margin-top:auto;">
        <p style="color:var(--gray); font-size:0.9rem;">&copy; 2025 Luminous Gym.</p>
    </footer>

    <script src="<?= BASE_URL ?>/scripts/dashboardAluno.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>