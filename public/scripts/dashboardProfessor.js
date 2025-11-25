// --- ESTADO DA APLICAÇÃO (MOCK DB) ---
// TODO: INTEGRAR BACKEND - Substituir este objeto pela resposta da API
const appState = {
    professor: {
        name: "Roberto",
        email: "roberto@luminousgym.com"
    },
    students: [
        { id: "1", name: "Carlos Eduardo", goal: "Hipertrofia", status: "ativo", lastUpdate: "Hoje", notes: "", workout: { seg: [], ter: [] } },
        { id: "2", name: "Fernanda Lima", goal: "Emagrecimento", status: "atrasado", lastUpdate: "15/11", notes: "", workout: { seg: [] } }
    ],
    currentId: null,
    currentDay: 'seg',
    editIndex: null
};

// --- INICIALIZAÇÃO ---
document.addEventListener('DOMContentLoaded', () => {
    // TODO: Fetch dados do professor e lista de alunos aqui
    renderTable();
    updateKPIs();
    updateProfileUI();
    lucide.createIcons();
});

// --- LÓGICA DE PERFIL (CONFIGURAÇÕES) ---
function openSettingsModal() {
    document.getElementById('settings-name').value = appState.professor.name;
    document.getElementById('settings-email').value = appState.professor.email;
    document.getElementById('overlay').classList.add('active');
    document.getElementById('modal-settings').classList.add('active');
}

function saveSettings() {
    const newName = document.getElementById('settings-name').value;
    const newEmail = document.getElementById('settings-email').value;

    // TODO: API CALL - Atualizar perfil do professor (PUT /professor)
    appState.professor.name = newName;
    appState.professor.email = newEmail;

    updateProfileUI();
    closeAll();
}

function updateProfileUI() {
    document.getElementById('header-prof-name').innerText = appState.professor.name;
}

// --- LÓGICA PRINCIPAL (TABELA E DADOS) ---
function renderTable() {
    const tbody = document.getElementById('students-table-body');
    tbody.innerHTML = '';

    if (appState.students.length === 0) {
        document.getElementById('empty-state').style.display = 'block';
        return;
    }
    document.getElementById('empty-state').style.display = 'none';

    appState.students.forEach(s => {
        const tr = document.createElement('tr');
        // CORREÇÃO: Adicionado aspas simples em '${s.id}' para suportar IDs string/UUID do banco de dados
        tr.innerHTML = `
            <td><strong>${s.name}</strong></td>
            <td><span class="badge badge-${s.status}">${s.status}</span></td>
            <td>${s.goal}</td>
            <td style="color:var(--gray)">${s.lastUpdate}</td>
            <td><button class="btn btn-outline" style="padding:0.4rem 1rem" onclick="openDrawer('${s.id}')">Gerenciar</button></td>
        `;
        tbody.appendChild(tr);
    });
    lucide.createIcons();
}

function updateKPIs() {
    document.getElementById('kpi-total').innerText = appState.students.length;
    document.getElementById('kpi-active').innerText = appState.students.filter(s => s.status === 'ativo').length;
    document.getElementById('kpi-late').innerText = appState.students.filter(s => s.status === 'atrasado').length;
}

// --- MODAIS E GESTÃO DE ALUNOS ---
function openNewStudentModal() {
    document.getElementById('overlay').classList.add('active');
    document.getElementById('modal-new-student').classList.add('active');
}

function closeAll() {
    document.getElementById('overlay').classList.remove('active');
    document.querySelectorAll('.modal-content').forEach(m => m.classList.remove('active'));
    document.getElementById('drawer').classList.remove('active');

    // Salva notas automaticamente ao fechar
    if (appState.currentId) {
        const s = appState.students.find(st => st.id === appState.currentId);
        if (s) {
            const notes = document.getElementById('teacher-notes').value;
            // TODO: API CALL - Salvar notas ao fechar o drawer (PATCH /student/{id})
            s.notes = notes;
        }
    }
}

function saveNewStudent() {
    const name = document.getElementById('new-name').value;
    if (!name) return alert("Nome é obrigatório.");

    const newStudent = {
        id: Date.now().toString(), // Gerando ID temporário
        name,
        goal: document.getElementById('new-goal').value,
        status: document.getElementById('new-status').value,
        lastUpdate: "Novo",
        notes: "",
        workout: {}
    };

    // TODO: API CALL - Criar novo aluno (POST /students)
    appState.students.push(newStudent);

    renderTable();
    updateKPIs();
    closeAll();
    document.getElementById('new-name').value = '';
}

function deleteCurrentStudent() {
    if (confirm("Tem certeza que deseja excluir este aluno? Esta ação não pode ser desfeita.")) {
        const index = appState.students.findIndex(s => s.id === appState.currentId);
        if (index > -1) {
            // TODO: API CALL - Excluir aluno (DELETE /student/{id})
            appState.students.splice(index, 1);
            renderTable();
            updateKPIs();
            closeAll();
        }
    }
}

// --- LÓGICA DO DRAWER E TREINOS ---
function openDrawer(id) {
    // O ID agora é tratado como string para compatibilidade com banco
    const s = appState.students.find(st => String(st.id) === String(id));
    if (!s) return;

    appState.currentId = id;
    appState.currentDay = 'seg'; // Reset para segunda-feira ao abrir
    resetForm();

    document.getElementById('drawer-name').innerText = s.name;
    document.getElementById('drawer-goal').innerText = s.goal;
    const badge = document.getElementById('drawer-status');
    badge.innerText = s.status;
    badge.className = `badge badge-${s.status}`;
    document.getElementById('teacher-notes').value = s.notes || '';

    renderTabs();
    renderExercises();

    document.getElementById('overlay').classList.add('active');
    document.getElementById('drawer').classList.add('active');
}

function renderTabs() {
    const days = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab'];
    document.getElementById('day-tabs').innerHTML = days.map(d => `
        <button class="tab-btn ${appState.currentDay === d ? 'active' : ''}" onclick="switchTab('${d}')">${d.toUpperCase()}</button>
    `).join('');

    // Atualiza o label no formulário
    document.getElementById('current-day-label').innerText = appState.currentDay.toUpperCase();
}

function switchTab(d) {
    appState.currentDay = d;
    resetForm();
    renderTabs();
    renderExercises();
}

function renderExercises() {
    const s = appState.students.find(st => st.id === appState.currentId);
    // Garante que não quebre se o dia ainda não tiver array
    const list = s.workout[appState.currentDay] || [];

    const container = document.getElementById('workout-list');
    container.innerHTML = '';

    if (list.length === 0) {
        container.innerHTML = `<p style="color:var(--gray); text-align:center; margin-bottom:1rem; font-style:italic;">Sem treino cadastrado para ${appState.currentDay.toUpperCase()}.</p>`;
    }

    list.forEach((ex, i) => {
        const div = document.createElement('div');
        div.className = 'exercise-item';
        div.innerHTML = `
            <div>
                <h4 style="color:white">${ex.name}</h4>
                <span style="font-size:0.85rem; color:var(--gray)">${ex.sets} Séries x ${ex.reps} Reps | ${ex.rest}</span>
            </div>
            <div style="display:flex; gap:0.5rem">
                <button class="btn-icon action-btn-edit" onclick="editEx(${i})"><i data-lucide="pencil" width="16"></i></button>
                <button class="btn-icon action-btn-del" onclick="delEx(${i})"><i data-lucide="trash-2" width="16"></i></button>
            </div>
        `;
        container.appendChild(div);
    });
    lucide.createIcons();
}

function handleExerciseSubmit() {
    const name = document.getElementById('ex-name').value;
    if (!name) return alert("Nome do exercício é obrigatório");

    const s = appState.students.find(st => st.id === appState.currentId);

    // Inicializa o array do dia se não existir
    if (!s.workout[appState.currentDay]) s.workout[appState.currentDay] = [];

    const data = {
        name,
        sets: document.getElementById('ex-sets').value,
        reps: document.getElementById('ex-reps').value,
        rest: document.getElementById('ex-rest').value
    };

    if (appState.editIndex !== null) {
        // Editando existente no dia atual
        s.workout[appState.currentDay][appState.editIndex] = data;
    } else {
        // Adicionando novo ao dia atual
        s.workout[appState.currentDay].push(data);
    }

    // TODO: API CALL - Salvar atualização do treino (PUT /student/{id}/workout)
    renderExercises();
    resetForm();
}

function editEx(i) {
    const s = appState.students.find(st => st.id === appState.currentId);
    const ex = s.workout[appState.currentDay][i];

    document.getElementById('ex-name').value = ex.name;
    document.getElementById('ex-sets').value = ex.sets;
    document.getElementById('ex-reps').value = ex.reps;
    document.getElementById('ex-rest').value = ex.rest;

    appState.editIndex = i;
    document.getElementById('form-title').querySelector('span').innerText = "Editando Exercício";
    document.getElementById('cancel-edit-btn').style.display = 'block';

    const btn = document.getElementById('btn-save-exercise');
    btn.innerHTML = 'Salvar Alteração';
    btn.classList.remove('btn-outline');
    btn.classList.add('btn-primary');
}

function delEx(i) {
    if (!confirm("Remover exercício?")) return;
    const s = appState.students.find(st => st.id === appState.currentId);
    s.workout[appState.currentDay].splice(i, 1);

    if (appState.editIndex === i) resetForm();
    renderExercises();
}

function resetForm() {
    document.getElementById('ex-name').value = '';
    document.getElementById('ex-sets').value = '';
    document.getElementById('ex-reps').value = '';
    document.getElementById('ex-rest').value = '';
    appState.editIndex = null;

    document.getElementById('form-title').querySelector('span').innerHTML = `Adicionar Exercício (<span id="current-day-label">${appState.currentDay.toUpperCase()}</span>)`;
    document.getElementById('cancel-edit-btn').style.display = 'none';

    const btn = document.getElementById('btn-save-exercise');
    btn.innerHTML = '<i data-lucide="plus"></i> Adicionar ao Dia Selecionado';
    btn.classList.add('btn-outline');
    btn.classList.remove('btn-primary');
    lucide.createIcons();
}