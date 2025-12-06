<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luminous Gym | Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="styles/dashboardProfessor.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>

    <header>
        <div class="container header-flex">
            <div style="display:flex; align-items:center; gap:0.5rem; font-weight:800; font-size:1.2rem;">
                <i data-lucide="dumbbell"></i> Luminous Gym
            </div>

            <!-- Menu do Usuário (Clicável para Configurações) -->
            <div class="user-area" onclick="openSettingsModal()" title="Clique para editar perfil">
                <div style="text-align: right;">
                    <span style="display:block; font-size: 0.75rem; color: var(--gray);">Professor</span>
                    <strong id="header-prof-name">Roberto</strong>
                </div>
                <div style="background: var(--medium-gray); padding: 8px; border-radius: 50%;">
                    <i data-lucide="settings" width="20"></i>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <!-- KPIs -->
        <section class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-value" id="kpi-total">0</div>
                <div class="kpi-label">Total de Alunos</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value" id="kpi-active">0</div>
                <div class="kpi-label">Treinos em Dia</div>
            </div>
            <div class="kpi-card" style="border-color: var(--red);">
                <div class="kpi-value" id="kpi-late" style="color: var(--red);">0</div>
                <div class="kpi-label">Atenção Necessária</div>
            </div>
        </section>

        <!-- Tabela -->
        <div class="table-header">
            <h2>Meus Alunos</h2>
            <button class="btn btn-primary" onclick="openNewStudentModal()">
                <i data-lucide="user-plus"></i> Novo Aluno
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Status</th>
                        <th>Objetivo</th>
                        <th>Último Treino</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody id="students-table-body"></tbody>
            </table>
            <div id="empty-state" style="padding: 3rem; text-align: center; color: var(--gray); display: none;">
                Nenhum aluno cadastrado.
            </div>
        </div>
    </main>

    <!-- Overlay Global -->
    <div id="overlay" class="overlay" onclick="closeAll()"></div>

    <!-- Modal: Configurações do Professor -->
    <div id="modal-settings" class="modal-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
            <h2>Configurações do Perfil</h2>
            <button class="btn-icon" onclick="closeAll()"><i data-lucide="x"></i></button>
        </div>

        <label style="display:block; margin-bottom:0.5rem">Nome de Exibição</label>
        <input type="text" id="settings-name" value="Roberto" style="margin-bottom:1rem">

        <label style="display:block; margin-bottom:0.5rem">Email de Acesso</label>
        <input type="email" id="settings-email" value="roberto@luminousgym.com" style="margin-bottom:2rem">

        <div style="text-align:right">
            <button class="btn btn-primary" onclick="saveSettings()">Salvar Alterações</button>
        </div>
    </div>

    <!-- Modal: Novo Aluno -->
    <div id="modal-new-student" class="modal-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
            <h2>Novo Aluno</h2>
            <button class="btn-icon" onclick="closeAll()"><i data-lucide="x"></i></button>
        </div>

        <label style="display:block; margin-bottom:0.5rem">Nome</label>
        <input type="text" id="new-name" placeholder="Ex: João Silva" style="margin-bottom:1rem">

        <label style="display:block; margin-bottom:0.5rem">Objetivo</label>
        <select id="new-goal" style="margin-bottom:1rem">
            <option>Hipertrofia</option>
            <option>Emagrecimento</option>
            <option>Resistência</option>
            <option>Força</option>
        </select>

        <label style="display:block; margin-bottom:0.5rem">Status</label>
        <select id="new-status" style="margin-bottom:1.5rem">
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
            <option value="atrasado">Atrasado</option>
        </select>

        <div style="text-align:right">
            <button class="btn btn-primary" onclick="saveNewStudent()">Cadastrar Aluno</button>
        </div>
    </div>

    <!-- Drawer: Detalhes do Aluno -->
    <aside id="drawer" class="drawer">
        <div
            style="padding: 2rem; background: var(--black); border-bottom: 1px solid var(--medium-gray); display: flex; justify-content: space-between;">
            <div>
                <h2 id="drawer-name">Nome</h2>
                <span id="drawer-status" class="badge badge-ativo">Ativo</span>
            </div>
            <button class="btn-icon" onclick="closeAll()"><i data-lucide="x"></i></button>
        </div>

        <div class="drawer-body">
            <p style="color: var(--gray); margin-bottom: 1.5rem;">Objetivo: <strong id="drawer-goal"
                    style="color: var(--white)">...</strong></p>

            <!-- Abas (Seg, Ter, Qua...) -->
            <div class="tabs" id="day-tabs"></div>

            <!-- Lista de Exercícios (Dinâmica por dia) -->
            <div id="workout-list"></div>

            <!-- Editor de Exercícios -->
            <div
                style="background: var(--black); padding: 1.5rem; border-radius: 8px; margin-top: 1rem; border: 1px dashed var(--gray);">
                <h4 id="form-title" style="margin-bottom: 1rem; display:flex; justify-content:space-between;">
                    <span>Adicionar Exercício (<span id="current-day-label">SEG</span>)</span>
                    <button id="cancel-edit-btn" class="btn-icon" style="width:20px; height:20px; display:none;"
                        onclick="resetForm()">
                        <i data-lucide="x" size="12"></i>
                    </button>
                </h4>
                <div class="form-row">
                    <input type="text" id="ex-name" placeholder="Ex: Supino">
                    <input type="text" id="ex-sets" placeholder="4">
                    <input type="text" id="ex-reps" placeholder="12">
                    <input type="text" id="ex-rest" placeholder="60s">
                </div>
                <button id="btn-save-exercise" class="btn btn-outline" style="width: 100%; justify-content: center;"
                    onclick="handleExerciseSubmit()">
                    <i data-lucide="plus"></i> Adicionar ao Dia Selecionado
                </button>
            </div>

            <!-- Notas -->
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--medium-gray);">
                <h3>Notas do Professor</h3>
                <textarea id="teacher-notes" rows="4" placeholder="Observações sobre o progresso..."></textarea>
            </div>

            <!-- Área de Perigo -->
            <div
                style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--medium-gray); text-align: center;">
                <button class="btn btn-danger" onclick="deleteCurrentStudent()" style="width: 100%;">
                    <i data-lucide="trash-2"></i> Excluir Aluno Permanentemente
                </button>
            </div>
        </div>
    </aside>
    <script src="scripts/dashboardProfessor.js"></script>
</body>

</html>